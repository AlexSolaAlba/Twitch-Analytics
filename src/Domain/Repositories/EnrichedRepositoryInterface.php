<?php

namespace TwitchAnalytics\Domain\Repositories;

interface EnrichedRepositoryInterface
{
    public function returnEnrichedStreamInfoFromAPI(int $limit, string $accessToken): array;
}
