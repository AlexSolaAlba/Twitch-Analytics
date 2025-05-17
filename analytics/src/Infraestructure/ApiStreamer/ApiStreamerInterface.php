<?php

namespace TwitchAnalytics\Infraestructure\ApiStreamer;

use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

interface ApiStreamerInterface
{
    public function getStreamerFromTwitch(int $streamerId, string $accessToken, DataBaseHandler $dataBaseHandler): Streamer;
}
