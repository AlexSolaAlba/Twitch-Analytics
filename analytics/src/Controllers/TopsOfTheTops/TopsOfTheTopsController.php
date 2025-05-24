<?php

namespace TwitchAnalytics\Controllers\TopsOfTheTops;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Application\Services\TopsOfTheTopsService;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Repositories\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class TopsOfTheTopsController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private TopsOfTheTopsValidator $topsValidator;
    private UserRepositoryInterface $userRepository;
    private TopsOfTheTopsService $topsService;


    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        TopsOfTheTopsValidator $topsValidator,
        UserRepositoryInterface $userRepository,
        TopsOfTheTopsService $topsService
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->topsValidator = $topsValidator;
        $this->userRepository = $userRepository;
        $this->topsService = $topsService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $twitchUser = $this->refreshTwitchToken->refreshTwitchToken();
            $since = $request->get('since');
            $this->topsValidator->validateSince($since);
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $this->userRepository->verifyUserToken($tokenUser);
            return response()->json($this->topsService->returnVideosInfo($twitchUser->getAccessToken(), $since));
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
