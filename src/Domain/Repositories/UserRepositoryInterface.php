<?php

namespace TwitchAnalytics\Domain\Repositories;

use TwitchAnalytics\Domain\Models\User;

interface UserRepositoryInterface
{
    public function registerUser(string $email, string $key): User;
    public function checkUserExists(string $email, string $key): User;
    public function assignTokenToUser(User $user, string $token): void;
    public function verifyUserToken(string $token): void;
}
