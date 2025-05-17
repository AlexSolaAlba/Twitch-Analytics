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
    public function returnStreamerInfo($userId, $accessToken): Streamer
    {
        $streamer = $this->streamerRepository->returnStreamerInfoFromDB($userId, $accessToken);
        if($streamer instanceof Streamer) {
            return $streamer;
        }
        return $this->streamerRepository->returnStreamerInfoFromAPI($userId, $accessToken);
    }
}
