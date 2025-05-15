<?php

namespace TwitchAnalytics\Infraestructure\ApiClient;

use TwitchAnalytics\Domain\Models\TwitchUser;

interface ApiTwitchClientInterface
{
    public function getTwitchAccessTokenFromApi(): TwitchUser;
}
