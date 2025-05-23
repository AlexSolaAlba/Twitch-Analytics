<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched;

interface ApiTwitchEnrichedInterface
{
    public function getEnrichedStreamsFromTwitch(int $limit, string $accessToken): array;
}
