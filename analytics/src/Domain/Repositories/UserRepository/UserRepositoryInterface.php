<?php

namespace TwitchAnalytics\Domain\Repositories\UserRepository;

use TwitchAnalytics\Domain\Models\User;

interface UserRepositoryInterface
{
    public function registerUser(string $email, string $key): User;
    public function checkUserExists(string $email, string $key): User;
}
