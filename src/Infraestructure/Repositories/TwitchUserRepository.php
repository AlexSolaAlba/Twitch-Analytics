<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Repositories\TwitchUserRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\ApiTwitchTokenInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class TwitchUserRepository implements TwitchUserRepositoryInterface
{
    private DataBaseHandler $databaseHandler;
    private ApiTwitchTokenInterface $apiTwitchClient;
    public function __construct(DataBaseHandler $databaseHandler, ApiTwitchTokenInterface $apiTwitchClient)
    {
        $this->databaseHandler = $databaseHandler;
        $this->apiTwitchClient = $apiTwitchClient;
    }

    public function getTwitchUser(): TwitchUser
    {
        return $this->databaseHandler->getTwitchUserFromDB();
    }

    public function getTwitchAccessToken(): TwitchUser
    {
        $twitchUser = $this->apiTwitchClient->getTwitchAccessTokenFromApi();
        $this->databaseHandler->updateTokenInDB($twitchUser->getAccessToken(), $twitchUser->getTokenExpire());
        return $twitchUser;
    }
}
