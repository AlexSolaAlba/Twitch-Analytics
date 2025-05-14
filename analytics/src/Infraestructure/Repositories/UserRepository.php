<?php

namespace TwitchAnalytics\Infraestructure\Repositories;

use TwitchAnalytics\Domain\Models\User;
use TwitchAnalytics\Domain\Repositories\UserRepository\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class UserRepository implements UserRepositoryInterface
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
    public function checkUserExists(string $email, string $key): User
    {
        return $this->databaseHandler->checkUserExistsInDB($email, $key);
    }
    public function assignTokenToUser(User $user, string $token): void
    {
        $this->databaseHandler->insertTokenIntoDB($user, $token);
    }
}
