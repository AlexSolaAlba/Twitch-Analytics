<?php

namespace TwitchAnalytics\Domain\Repositories\StreamerRepository;

use TwitchAnalytics\Domain\Models\Streamer;

interface StreamerRepositoryInterface
{
    public function returnStreamerInfoFromDB($userId, $accessToken): bool|Streamer;
    public function returnStreamerInfoFromAPI($userId, $accessToken): Streamer;
}
