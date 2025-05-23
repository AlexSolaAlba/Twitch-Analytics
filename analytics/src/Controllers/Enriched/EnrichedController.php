<?php

namespace TwitchAnalytics\Controllers\Enriched;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
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

    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        EnrichedValidator $enrichedValidator,
        UserRepositoryInterface $userRepository,
        ApiTwitchEnrichedInterface $apiTwitchEnriched,
    ) {
        $this->userRepository = $userRepository;
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->enrichedValidator = $enrichedValidator;
        $this->apiTwitchEnriched = $apiTwitchEnriched;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $twitchUser = $this->refreshTwitchToken->refreshTwitchToken();
            $this->enrichedValidator->validateLimit($request->get('limit'));
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $this->userRepository->verifyUserToken($tokenUser);

            $streamObjects = $this->apiTwitchEnriched->getEnrichedStreamsFromTwitch($request->get('limit'), $twitchUser->getAccessToken());
            $streams = array_map(fn($stream) => [
                'streamerId' => $stream->getStreamerId(),
                'userId' => $stream->getUserId(),
                'userName' => $stream->getUserName(),
                'viewerCount' => $stream->getViewerCount(),
                'userDisplayName' => $stream->getUserDisplayName(),
                'title' => $stream->getTitle(),
                'profileImageUrl' => $stream->getProfileImageUrl(),
            ], $streamObjects);

            return response()->json($streams);
            #return response()->json($this->apiTwitchEnriched->getEnrichedStreamsFromTwitch($request->get('limit'), $twitchUser->getAccessToken()));
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
