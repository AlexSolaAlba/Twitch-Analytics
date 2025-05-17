<?php

namespace TwitchAnalytics\Domain\Models;

class Streamer
{
    private string $streamerId;
    private string $login;
    private string $displayName;
    private string $type;
    private string $broadcasterType;
    private string $description;
    private string $profileImageUrl;
    private string $offlineImageUrl;
    private string $viewCount;
    private string $createdAt;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $streamerId,
        string $login,
        string $displayName,
        string $type,
        string $broadcasterType,
        string $description,
        string $profileImageUrl,
        string $offlineImageUrl,
        string $viewCount,
        string $createdAt
    ) {
        $this->streamerId = $streamerId;
        $this->login = $login;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->broadcasterType = $broadcasterType;
        $this->description = $description;
        $this->profileImageUrl = $profileImageUrl;
        $this->offlineImageUrl = $offlineImageUrl;
        $this->viewCount = $viewCount;
        $this->createdAt = $createdAt;
    }

    public function getStreamerId(): string
    {
        return $this->streamerId;
    }
    public function getLogin(): string
    {
        return $this->login;
    }
    public function getDisplayName(): string
    {
        return $this->displayName;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getBroadcasterType(): string
    {
        return $this->broadcasterType;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getProfileImageUrl(): string
    {
        return $this->profileImageUrl;
    }
    public function getOfflineImageUrl(): string
    {
        return $this->offlineImageUrl;
    }
    public function getViewCount(): string
    {
        return $this->viewCount;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setStreamerId(string $streamerId): void
    {
        $this->streamerId = $streamerId;
    }
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    public function setBroadcasterType(string $broadcasterType): void
    {
        $this->broadcasterType = $broadcasterType;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function setProfileImageUrl(string $profileImageUrl): void
    {
        $this->profileImageUrl = $profileImageUrl;
    }
    public function setOfflineImageUrl(string $offlineImageUrl): void
    {
        $this->offlineImageUrl = $offlineImageUrl;
    }
    public function setViewCount(string $viewCount): void
    {
        $this->viewCount = $viewCount;
    }
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
