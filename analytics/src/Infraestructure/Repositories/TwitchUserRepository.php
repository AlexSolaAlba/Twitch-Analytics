<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class TwitchUserRepository
{
    private DataBaseHandler $databaseHandler;
    public function __construct(DataBaseHandler $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
    }

    public function getTwitchUser(): TwitchUser
    {
        return $this->databaseHandler->getTwitchUserFromDB();
    }
}
