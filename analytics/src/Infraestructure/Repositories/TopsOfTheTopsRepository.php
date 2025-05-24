<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Repositories\TopsOfTheTopsRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\ApiTwitchVideos;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandlerVideos;

class TopsOfTheTopsRepository implements TopsOfTheTopsRepositoryInterface
{
    private ApiTwitchVideos $apiVideos;
    private DataBaseHandlerVideos $dataBaseHandler;
    public function __construct(ApiTwitchVideos $apiVideos, DataBaseHandlerVideos $dataBaseHandler)
    {
        $this->apiVideos = $apiVideos;
        $this->dataBaseHandler = $dataBaseHandler;
    }
    public function returnVideosInfoFromAPI(string $accessToken): array
    {
        return $this->apiVideos->getVideosFromTwitch($accessToken);
    }

    public function readCache(): bool|array
    {
        return $this->dataBaseHandler->getVideosFromDB();
    }

    public function deleteCache(): void
    {
        $this->dataBaseHandler->deleteAllVideosFromDB();
    }

    public function updateCache(array $videos): void
    {
        $this->dataBaseHandler->insertVideosInDB($videos);
    }
}
