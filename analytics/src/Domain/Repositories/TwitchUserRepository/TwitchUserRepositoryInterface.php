<?php

namespace TwitchAnalytics\Domain\Repositories\TwitchUserRepository;

use TwitchAnalytics\Domain\Models\TwitchUser;

interface TwitchUserRepositoryInterface
{
    public function getTwitchUser(): TwitchUser;
    public function getTwitchAccessToken(): TwitchUser;
}
