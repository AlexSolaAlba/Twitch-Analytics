<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Repositories\TopsOfTheTopsRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\ApiTwitchVideos;

class TopsOfTheTopsRepository implements TopsOfTheTopsRepositoryInterface
{
    private ApiTwitchVideos $apiVideos;
    public function __construct(ApiTwitchVideos $apiVideos)
    {
        $this->apiVideos = $apiVideos;
    }
    public function returnVideosInfoFromAPI(string $accessToken): array
    {
        return $this->apiVideos->getVideosFromTwitch($accessToken);
    }
}
