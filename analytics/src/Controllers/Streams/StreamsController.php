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
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\ApiTwitchStreamsInterface;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;
use TwitchAnalytics\Domain\Models\Stream;

class StreamsController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private UserRepositoryInterface $userRepository;
    private ApiTwitchStreamsInterface $apiTwitchStreams;
    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        UserRepositoryInterface $userRepository,
        ApiTwitchStreamsInterface $apiTwitchStreams
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->userRepository = $userRepository;
        $this->apiTwitchStreams = $apiTwitchStreams;
    }

    public function __invoke(Request $request): JsonResponse
    {

        try {
            $twitchUser = $this->refreshTwitchToken->refreshTwitchToken();
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $this->userRepository->verifyUserToken($tokenUser);

            #return response()->json($this->apiTwitchStreams->getStreamsFromTwitch($twitchUser->getAccessToken()));
            $streamObjects = $this->apiTwitchStreams->getStreamsFromTwitch($twitchUser->getAccessToken());

            $streams = array_map(fn($Stream) => [
                'title' => $Stream->getStreamTitle(),
                'user_name' => $Stream->getStreamUserName(),
            ], $streamObjects);

            return response()->json($streams);
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
