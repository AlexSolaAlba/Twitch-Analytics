<?php

namespace TwitchAnalytics\Domain\Models;

class TwitchUser
{
    private int $tokenID;
    private string $accessToken;
    private int $tokenExpire;

    public function __construct(int $tokenID, string $accessToken, int $tokenExpire)
    {
        $this->tokenID = $tokenID;
        $this->accessToken = $accessToken;
        $this->tokenExpire = $tokenExpire;
    }

    public function getTokenID(): int
    {
        return $this->tokenID;
    }

    public function setTokenID(int $tokenID): void
    {
        $this->tokenID = $tokenID;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getTokenExpire(): int
    {
        return $this->tokenExpire;
    }

    public function setTokenExpire(int $tokenExpire): void
    {
        $this->tokenExpire = $tokenExpire;
    }
}
