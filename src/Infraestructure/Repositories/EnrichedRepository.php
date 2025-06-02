<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Repositories\EnrichedRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\ApiTwitchEnrichedInterface;

class EnrichedRepository implements EnrichedRepositoryInterface
{
    private ApiTwitchEnrichedInterface $apiStream;
    public function __construct(ApiTwitchEnrichedInterface $apiStream)
    {
        $this->apiStream = $apiStream;
    }
    public function returnEnrichedStreamInfoFromAPI(int $limit, string $accessToken): array
    {
        return $this->apiStream->getEnrichedStreamsFromTwitch($limit, $accessToken);
    }
}
