<?php

namespace TwitchAnalytics\Domain\Models;

class Stream
{
    private string $title;
    private string $userName;

    public function __construct(string $title, string $userName)
    {
        $this->title = $title;
        $this->userName = $userName;
    }
    public function getStreamTitle(): string
    {
        return $this->title;
    }
    public function getStreamUserName(): string
    {
        return $this->userName;
    }
    public function setStreamTittle(string $title): void
    {
        $this->title = $title;
    }

    public function setStreamUserName(string $userName): void
    {
        $this->userName = $userName;
    }
}
