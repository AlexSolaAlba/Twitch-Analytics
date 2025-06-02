<?php

namespace TwitchAnalytics\Domain\Repositories;

interface TopsOfTheTopsRepositoryInterface
{
    public function returnVideosInfoFromAPI(string $accessToken): array;
}
