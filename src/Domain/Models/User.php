<?php

namespace TwitchAnalytics\Domain\Models;

class User
{
    private int $userId;
    private string $email;
    private string $apiKey;
    private string $token;
    private int $tokenExpire;
    public function __construct(int $userId, string $email, string $apiKey, string $token, int $tokenExpire)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->apiKey = $apiKey;
        $this->token = $token;
        $this->tokenExpire = $tokenExpire;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTokenExpire(): int
    {
        return $this->tokenExpire;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setTokenExpire(int $tokenExpire): void
    {
        $this->tokenExpire = $tokenExpire;
    }
}
