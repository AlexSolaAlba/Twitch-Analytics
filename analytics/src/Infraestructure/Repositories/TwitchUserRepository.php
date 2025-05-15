<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Repositories\TwitchUserRepository\TwitchUserRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchClientInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class TwitchUserRepository implements TwitchUserRepositoryInterface
{
    private DataBaseHandler $databaseHandler;
    private ApiTwitchClientInterface $apiTwitchClient;
    public function __construct(DataBaseHandler $databaseHandler, ApiTwitchClientInterface $apiTwitchClient)
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
