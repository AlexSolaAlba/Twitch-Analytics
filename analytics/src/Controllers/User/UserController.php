<?php

namespace TwitchAnalytics\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Repositories\UserRepository\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiStreamer\ApiStreamerInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class UserController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private UserRepositoryInterface $userRepository;
    private DataBaseHandler $dataBaseHandler;
    private ApiStreamerInterface $apiStreamer;
    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        UserRepositoryInterface $userRepository,
        DataBaseHandler $dataBaseHandler,
        ApiStreamerInterface $apiStreamer
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->userRepository = $userRepository;
        $this->dataBaseHandler = $dataBaseHandler;
        $this->apiStreamer = $apiStreamer;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->refreshTwitchToken->refreshTwitchToken();
            $this->userValidator->validateUserId($request->get('id'));
            $tokenUser = $this->userValidator->validateToken($request->header('Authorization'));
            $user = $this->userRepository->verifyUserToken($tokenUser);

            return response()->json($this->returnStreamerInfo($request->get('id'), $user->getToken()));
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
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function returnStreamerInfo($userId, $accessToken): array
    {
        $connection = $this->dataBaseHandler->connectWithDB();
        $this->dataBaseHandler->checkConnection($connection);

        try {
            $stmt = $connection->prepare("SELECT * FROM usersTwitch where id = ?");
            $stmt->bind_param("i", $userId);
            $this->dataBaseHandler->checkStmtExecution($stmt);
            $streamerRaw = $stmt->get_result();

            if ($streamerRaw->num_rows > 0) {
                $streamer = $streamerRaw->fetch_assoc();
                return [
                    "id" => $streamer["id"],
                    "login" => $streamer["user_login"],
                    "display_name" => $streamer["display_name"],
                    "type" => $streamer["user_type"],
                    "broadcaster_type" => $streamer["broadcaster_type"],
                    "description" => $streamer["user_description"],
                    "profile_image_url" => $streamer["profile_image_url"],
                    "offline_image_url" => $streamer["offline_image_url"],
                    "view_count" => $streamer["view_count"],
                    "created_at" => $streamer["created_at"]
                ];
            } else {
                $streamer = $this->apiStreamer->getStreamerFromTwitch($userId, $accessToken, $this->dataBaseHandler);
                return [
                    "id" => $streamer->getStreamerId(),
                    "login" => $streamer->getLogin(),
                    "display_name" => $streamer->getDisplayName(),
                    "type" => $streamer->getType(),
                    "broadcaster_type" => $streamer->getBroadcasterType(),
                    "description" => $streamer->getDescription(),
                    "profile_image_url" => $streamer->getProfileImageUrl(),
                    "offline_image_url" => $streamer->getOfflineImageUrl(),
                    "view_count" => $streamer->getViewCount(),
                    "created_at" => $streamer->getCreatedAt()
                ];
            }
        } finally {
            $connection->close();
        }
    }
}
