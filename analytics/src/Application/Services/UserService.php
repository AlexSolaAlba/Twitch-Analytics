<?php

namespace TwitchAnalytics\Application\Services;

use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Domain\Repositories\StreamerRepository\StreamerRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiStreamer\ApiStreamerInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class UserService
{
    private StreamerRepositoryInterface $streamerRepository;
    public function __construct(StreamerRepositoryInterface $streamerRepository)
    {
        $this->streamerRepository = $streamerRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function returnStreamerInfo($userId, $accessToken): array
    {
        $streamer = $this->streamerRepository->returnStreamerInfoFromDB($userId, $accessToken);
        if ($streamer instanceof Streamer) {
            return [
                'id' => $streamer->getStreamerId(),
                'login' => $streamer->getLogin(),
                'display_name' => $streamer->getDisplayName(),
                'type' => $streamer->getType(),
                'broadcaster_type' => $streamer->getBroadcasterType(),
                'description' => $streamer->getDescription(),
                'profile_image_url' => $streamer->getProfileImageUrl(),
                'offline_image_url' => $streamer->getOfflineImageUrl(),
                'view_count' => $streamer->getViewCount(),
                'created_at' => $streamer->getCreatedAt(),
            ];
        }
        $streamer = $this->streamerRepository->returnStreamerInfoFromAPI($userId, $accessToken);
        return [
            'id' => $streamer->getStreamerId(),
            'login' => $streamer->getLogin(),
            'display_name' => $streamer->getDisplayName(),
            'type' => $streamer->getType(),
            'broadcaster_type' => $streamer->getBroadcasterType(),
            'description' => $streamer->getDescription(),
            'profile_image_url' => $streamer->getProfileImageUrl(),
            'offline_image_url' => $streamer->getOfflineImageUrl(),
            'view_count' => $streamer->getViewCount(),
            'created_at' => $streamer->getCreatedAt(),
        ];
    }
}
