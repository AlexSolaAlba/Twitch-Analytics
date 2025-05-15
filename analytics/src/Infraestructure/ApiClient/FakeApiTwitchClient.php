<?php

namespace TwitchAnalytics\Infraestructure\ApiClient;

use TwitchAnalytics\Domain\Models\TwitchUser;

class FakeApiTwitchClient implements ApiTwitchClientInterface
{
    private array $fakeTwitchUser = [
            "access_token" => "jostpf5q0puzmxmkba9iyug38kjtg",
            "expires_in" => 5011271,
            "token_type" => "bearer"
    ];

    public function getTwitchAccessTokenFromApi(): TwitchUser
    {
        return new TwitchUser(
            1,
            $this->fakeTwitchUser['access_token'],
            $this->fakeTwitchUser['expires_in']
        );
    }
}
