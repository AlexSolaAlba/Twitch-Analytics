<?php

namespace TwitchAnalytics\Domain\Models;

class User
{
    private int $id;
    private string $email;
    private string $apiKey;
    private string $token;
    private int $tokenExpire;
    public function __construct(int $id, string $email, string $apiKey, string $token, int $tokenExpire)
    {
        $this->id = $id;
        $this->email = $email;
        $this->apiKey = $apiKey;
        $this->token = $token;
        $this->tokenExpire = $tokenExpire;
    }

    public function getId(): int
    {
        return $this->id;
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
