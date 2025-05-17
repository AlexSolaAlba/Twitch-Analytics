<?php

namespace TwitchAnalytics\Domain\Repositories\StreamerRepository;

use TwitchAnalytics\Domain\Models\Streamer;

interface StreamerRepositoryInterface
{
    public function returnStreamerInfoFromDB(int $streamerId, string $accessToken): bool|Streamer;
    public function returnStreamerInfoFromAPI(int $streamerId, string $accessToken): Streamer;
}
