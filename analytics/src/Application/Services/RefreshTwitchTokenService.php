<?php

namespace TwitchAnalytics\Application\Services;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Repositories\TwitchUserRepository\TwitchUserRepositoryInterface;
use TwitchAnalytics\Domain\Time\TimeProviderInterface;
use TwitchAnalytics\Infraestructure\Exceptions\DBException;

class RefreshTwitchTokenService
{
    private TwitchUserRepositoryInterface $twitchUserRepository;
    private TimeProviderInterface $timeProvider;
    public function __construct(TwitchUserRepositoryInterface $twitchUserRepository, TimeProviderInterface $timeProvider)
    {
        $this->twitchUserRepository = $twitchUserRepository;
        $this->timeProvider = $timeProvider;
    }
    public function refreshTwitchToken(): TwitchUser
    {
        try {
            $twitchUser = $this->twitchUserRepository->getTwitchUser();
            if ($this->timeProvider->now() >= $twitchUser->getTokenExpire()) {
                return $this->twitchUserRepository->getTwitchAccessToken();
            }
            return $twitchUser;
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        } catch (TwitchApiException $e) {
            throw new TwitchApiException($e->getMessage());
        }
    }
}
