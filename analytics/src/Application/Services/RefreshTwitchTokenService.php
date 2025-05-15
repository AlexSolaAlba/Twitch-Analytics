<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Repositories\TwitchUserRepository\TwitchUserRepositoryInterface;
use TwitchAnalytics\Infraestructure\DB\DBException;

class RefreshTwitchTokenService
{
    private TwitchUserRepositoryInterface $twitchUserRepository;
    public function __construct(TwitchUserRepositoryInterface $twitchUserRepository)
    {
        $this->twitchUserRepository = $twitchUserRepository;
    }
    public function refreshTwitchToken(string $token): TwitchUser
    {
        /*$this->userRepository->verifyUserToken($token);*/
        try {
            $twitchUser = $this->twitchUserRepository->getTwitchUser();
            if (time() >= $twitchUser->getTokenExpire()) {
                return $this->twitchUserRepository->getTwitchAccessToken();
            }
            return $twitchUser;
        } catch (ApiKeyException $e) {
            throw new ApiKeyException($e->getMessage());
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        } catch (TwitchApiException $e) {
            throw new TwitchApiException($e->getMessage());
        }
    }
}
