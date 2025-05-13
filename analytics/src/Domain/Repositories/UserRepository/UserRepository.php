<?php

namespace TwitchAnalytics\Domain\Repositories\UserRepository;

use TwitchAnalytics\Domain\DB\DataBaseHandler;
use TwitchAnalytics\Domain\Models\User;

class UserRepository
{
    private DataBaseHandler $databaseHandler;
    public function __construct(DataBaseHandler $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
    }

    public function registerUser(string $email, string $key): User
    {
        return $this->databaseHandler->saveUserAndApiKeyInDB($email, $key);
    }
}
