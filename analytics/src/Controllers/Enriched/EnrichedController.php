<?php

namespace TwitchAnalytics\Controllers\Enriched;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\EnrichedService;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Repositories\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\ApiTwitchEnriched;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\ApiTwitchEnrichedInterface;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class EnrichedController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private EnrichedValidator $enrichedValidator;
    private UserRepositoryInterface $userRepository;
    private ApiTwitchEnrichedInterface $apiTwitchEnriched;
    private EnrichedService $enrichedService;

    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        EnrichedValidator $enrichedValidator,
        UserRepositoryInterface $userRepository,
        ApiTwitchEnrichedInterface $apiTwitchEnriched,
        EnrichedService $enrichedService
    ) {
        $this->userRepository = $userRepository;
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->enrichedValidator = $enrichedValidator;
        $this->apiTwitchEnriched = $apiTwitchEnriched;
        $this->enrichedService = $enrichedService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $twitchUser = $this->refreshTwitchToken->refreshTwitchToken();
            $this->enrichedValidator->validateLimit($request->get('limit'));
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $this->userRepository->verifyUserToken($tokenUser);

            return response()->json($this->enrichedService->returnEnrichedStreamsInfo($request->get('limit'), $twitchUser->getAccessToken()));
        } catch (ApiKeyException $ex) {
            return response()->json(['error' => $ex->getMessage()], 401);
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (NotFoundException $ex) {
            return response()->json(['error' => $ex->getMessage()], 404);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
