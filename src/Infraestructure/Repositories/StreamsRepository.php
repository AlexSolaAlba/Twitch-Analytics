<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\ApiTwitchStreamsInterface;

class StreamsRepository implements StreamsRepositoryInterface
{
    private ApiTwitchStreamsInterface $apiStreamer;
    public function __construct(ApiTwitchStreamsInterface $apiStreamer)
    {
        $this->apiStreamer = $apiStreamer;
    }

    public function returnStreamsInfoFromAPI(string $accessToken): array
    {
        return $this->apiStreamer->getStreamsFromTwitch($accessToken);
    }
}
