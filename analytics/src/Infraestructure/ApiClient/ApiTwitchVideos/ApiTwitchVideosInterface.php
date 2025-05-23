<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos;

interface ApiTwitchVideosInterface
{
    public function getGamesFromTwitch($accessToken): array;
}
