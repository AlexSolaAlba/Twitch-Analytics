<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Domain\Repositories\StreamerRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreamer\ApiTwitchStreamerInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class StreamerRepository implements StreamerRepositoryInterface
{
    private DatabaseHandler $dataBaseHandler;
    private ApiTwitchStreamerInterface $apiStreamer;
    public function __construct(DatabaseHandler $dataBaseHandler, ApiTwitchStreamerInterface $apiStreamer)
    {
        $this->dataBaseHandler = $dataBaseHandler;
        $this->apiStreamer = $apiStreamer;
    }

    public function returnStreamerInfoFromDB(int $streamerId, string $accessToken): bool|Streamer
    {
        return $this->dataBaseHandler->getStreamerFromDB($streamerId);
    }

    public function returnStreamerInfoFromAPI(int $streamerId, string $accessToken): Streamer
    {
        $streamer = $this->apiStreamer->getStreamerFromTwitch($streamerId, $accessToken);
        $this->dataBaseHandler->insertStreamerIntoDB($streamer);
        return $streamer;
    }
}
