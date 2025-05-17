<?php

namespace TwitchAnalytics\Application\Services;

use TwitchAnalytics\Infraestructure\ApiStreamer\ApiStreamerInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class UserService
{
    private DataBaseHandler $dataBaseHandler;
    private ApiStreamerInterface $apiStreamer;
    public function __construct(DataBaseHandler $databaseHandler, ApiStreamerInterface $apiStreamer)
    {
        $this->dataBaseHandler = $databaseHandler;
        $this->apiStreamer = $apiStreamer;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function returnStreamerInfo($userId, $accessToken): array
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
