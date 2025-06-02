<?php

namespace TwitchAnalytics\Domain\Repositories;

interface StreamsRepositoryInterface
{
    public function returnStreamsInfoFromAPI(string $accessToken): array;
}
