<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams;

interface ApiTwitchStreamsInterface
{
    public function getStreamsFromTwitch($accessToken): array;
}
