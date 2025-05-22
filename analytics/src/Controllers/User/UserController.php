<?php

namespace TwitchAnalytics\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Application\Services\UserService;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Repositories\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class UserController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private UserRepositoryInterface $userRepository;
    private UserService $userService;
    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        UserRepositoryInterface $userRepository,
        UserService $userService
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $twitchUser = $this->refreshTwitchToken->refreshTwitchToken();
            $this->userValidator->validateUserId($request->get('id'));
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $this->userRepository->verifyUserToken($tokenUser);

            return response()->json($this->userService->returnStreamerInfo($request->get('id'), $twitchUser->getAccessToken()));
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
