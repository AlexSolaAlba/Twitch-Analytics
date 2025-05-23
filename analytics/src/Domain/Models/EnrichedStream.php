<?php

namespace TwitchAnalytics\Domain\Models;

class EnrichedStream
{
    private string $streamerId;
    private string $userId;
    private string $userName;
    private string $viewerCount;
    private string $userDisplayName;
    private string $title;
    private string $profileImageUrl;

    public function __construct(
        string $streamerId,
        string $userId,
        string $userName,
        string $viewerCount,
        string $userDisplayName,
        string $title,
        string $profileImageUrl
    ) {
        $this->streamerId = $streamerId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->viewerCount = $viewerCount;
        $this->userDisplayName = $userDisplayName;
        $this->title = $title;
        $this->profileImageUrl = $profileImageUrl;
    }

    public function getStreamerId(): string
    {
        return $this->streamerId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getViewerCount(): string
    {
        return $this->viewerCount;
    }

    public function getUserDisplayName(): string
    {
        return $this->userDisplayName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getProfileImageUrl(): string
    {
        return $this->profileImageUrl;
    }
}
