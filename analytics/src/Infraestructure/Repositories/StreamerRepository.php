<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Domain\Repositories\StreamerRepository\StreamerRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiStreamer\ApiStreamerInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class StreamerRepository implements StreamerRepositoryInterface
{
    private DatabaseHandler $databaseHandler;
    private ApiStreamerInterface $apiStreamer;
    public function __construct(DatabaseHandler $databaseHandler, ApiStreamerInterface $apiStreamer)
    {
        $this->databaseHandler = $databaseHandler;
        $this->apiStreamer = $apiStreamer;
    }

    public function returnStreamerInfoFromDB($userId, $accessToken): bool|Streamer
    {
        return $this->databaseHandler->getStreamerFromDB($userId);
    }

    public function returnStreamerInfoFromAPI($userId, $accessToken): Streamer
    {
        $streamer = $this->apiStreamer->getStreamerFromTwitch($userId, $accessToken);
        $this->databaseHandler->insertStreamerIntoDB($streamer);
        return $streamer;
    }
}
