<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken;

use TwitchAnalytics\Domain\Models\TwitchUser;

interface ApiTwitchTokenInterface
{
    public function getTwitchAccessTokenFromApi(): TwitchUser;
}
