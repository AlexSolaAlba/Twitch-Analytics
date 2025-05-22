<?php

namespace TwitchAnalytics\Controllers\Streams;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Repositories\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class StreamsController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private UserRepositoryInterface $userRepository;
    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        UserRepositoryInterface $userRepository,
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {

        try {
            $this->refreshTwitchToken->refreshTwitchToken();
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $user = $this->userRepository->verifyUserToken($tokenUser);

            $curl = curl_init();
            $clientID = env('CLIENT_ID');
            $accesstoken = $user->getToken();
            curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/streams?first=100");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Client-ID: $clientID",
                "Authorization: Bearer $accesstoken"
            ]);

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $response_data = json_decode($response, true);

            $filtered_streams = [];

            if (isset($response_data['data'])) {
                foreach ($response_data['data'] as $stream) {
                    $filtered_streams[] = [
                        'title' => $stream['title'],
                        'user_name' => $stream['user_name']
                    ];
                }
            }

            if ($http_code == 401) {
                $error_message = [
                    'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
                ];
                return response()->json($error_message, 401);
            }
            if ($http_code == 500) {
                $error_message = [
                    'error' => 'Internal server error.'
                ];
                return response()->json($error_message, 500);
            }
            return response()->json($filtered_streams);
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
