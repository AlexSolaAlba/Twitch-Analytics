<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreamer;

use TwitchAnalytics\Domain\Models\Streamer;

interface ApiTwitchStreamerInterface
{
    public function getStreamerFromTwitch(int $streamerId, string $accessToken): Streamer;
}
