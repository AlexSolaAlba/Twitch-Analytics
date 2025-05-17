<?php

namespace TwitchAnalytics\Infraestructure\ApiStreamer;

use TwitchAnalytics\Domain\Models\Streamer;

interface ApiStreamerInterface
{
    public function getStreamerFromTwitch($userId): Streamer;
}
