<?php

namespace TwitchAnalytics\Domain\Models;

class TwitchUser
{
    private int $tokenID;
    private string $accessToken;
    private int $tokenExpire;
    private string $clientID;
    private string $clientSecret;
    public function __construct(int $tokenID, string $accessToken, int $tokenExpire, string $clientId, string $clientSecret)
    {
        $this->tokenID = $tokenID;
        $this->accessToken = $accessToken;
        $this->tokenExpire = $tokenExpire;
        $this->clientID = $clientId;
        $this->clientSecret = $clientSecret;
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

    public function getClientID(): string
    {
        return $this->clientID;
    }

    public function setClientID(string $clientID): void
    {
        $this->clientID = $clientID;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }
}
