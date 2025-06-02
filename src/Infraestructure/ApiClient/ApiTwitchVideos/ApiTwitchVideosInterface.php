<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos;

interface ApiTwitchVideosInterface
{
    public function getVideosFromTwitch($accessToken): array;
}
