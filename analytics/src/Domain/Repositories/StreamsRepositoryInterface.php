<?php

namespace TwitchAnalytics\Domain\Repositories;

use TwitchAnalytics\Domain\Models\Stream;

interface StreamsRepositoryInterface
{
    public function returnStreamsInfoFromAPI(string $accessToken): array;
}
