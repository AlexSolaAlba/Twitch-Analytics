<?php

namespace TwitchAnalytics\Domain\Models;

class Video
{
    private int $gameId;
    private string $gameName;
    private string $userName;
    private int $totalVideos;
    private int $totalViews;
    private string $mostViewedTitle;
    private int $mostViewedViews;
    private string $mostViewedDuration;
    private string $mostViewedCreatedAt;
    private string $createdAt;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        int $gameId,
        string $gameName,
        string $userName,
        int $totalVideos,
        int $totalViews,
        string $mostViewedTitle,
        int $mostViewedViews,
        string $mostViewedDuration,
        string $mostViewedCreatedAt,
        string $createdAt = null
    ) {
        $this->gameId = $gameId;
        $this->gameName = $gameName;
        $this->userName = $userName;
        $this->totalVideos = $totalVideos;
        $this->totalViews = $totalViews;
        $this->mostViewedTitle = $mostViewedTitle;
        $this->mostViewedViews = $mostViewedViews;
        $this->mostViewedDuration = $mostViewedDuration;
        $this->mostViewedCreatedAt = $mostViewedCreatedAt;
        $this->createdAt = $createdAt;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }

    public function setGameId(int $gameId): void
    {
        $this->gameId = $gameId;
    }

    public function getGameName(): string
    {
        return $this->gameName;
    }

    public function setGameName(string $gameName): void
    {
        $this->gameName = $gameName;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getTotalVideos(): int
    {
        return $this->totalVideos;
    }

    public function setTotalVideos(int $totalVideos): void
    {
        $this->totalVideos = $totalVideos;
    }

    public function getTotalViews(): int
    {
        return $this->totalViews;
    }

    public function setTotalViews(int $totalViews): void
    {
        $this->totalViews = $totalViews;
    }

    public function getMostViewedTitle(): string
    {
        return $this->mostViewedTitle;
    }

    public function setMostViewedTitle(string $mostViewedTitle): void
    {
        $this->mostViewedTitle = $mostViewedTitle;
    }

    public function getMostViewedViews(): int
    {
        return $this->mostViewedViews;
    }

    public function setMostViewedViews(int $mostViewedViews): void
    {
        $this->mostViewedViews = $mostViewedViews;
    }

    public function getMostViewedDuration(): string
    {
        return $this->mostViewedDuration;
    }

    public function setMostViewedDuration(string $mostViewedDuration): void
    {
        $this->mostViewedDuration = $mostViewedDuration;
    }

    public function getMostViewedCreatedAt(): string
    {
        return $this->mostViewedCreatedAt;
    }

    public function setMostViewedCreatedAt(string $mostViewedCreatedAt): void
    {
        $this->mostViewedCreatedAt = $mostViewedCreatedAt;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
