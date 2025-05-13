<?php

namespace TwitchAnalytics\Domain\Models;

class User
{
    private string $email;
    private string $apiKey;
    private string $token;
    private int $tokenExpire;
    public function __construct(string $email, string $apiKey, string $token, int $tokenExpire)
    {
        $this->email = $email;
        $this->apiKey = $apiKey;
        $this->token = $token;
        $this->tokenExpire = $tokenExpire;
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
}
