<?php

namespace TwitchAnalytics\Infraestructure\ApiStreamer;

use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class FakeApiStreamer implements ApiStreamerInterface
{
    private array $FakeStreamer = [
        "id" => "4",
        "login" => "elsmurfoz",
        "display_name" => "elsmurfoz",
        "type" => "",
        "broadcaster_type" => "",
        "description" => "",
        "profile_image_url" => "https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png",
        "offline_image_url" => "",
        "view_count" => "0",
        "created_at" => "2007-05-22T10:37:47Z"
    ];
    public function getStreamerFromTwitch(int $streamerId, string $accessToken): Streamer
    {
        if ($streamerId !== 4) {
            throw new NotFoundException('User not found.');
        }
        return new Streamer(
            $this->FakeStreamer['id'],
            $this->FakeStreamer['login'],
            $this->FakeStreamer['display_name'],
            $this->FakeStreamer['type'],
            $this->FakeStreamer['broadcaster_type'],
            $this->FakeStreamer['description'],
            $this->FakeStreamer['profile_image_url'],
            $this->FakeStreamer['offline_image_url'],
            $this->FakeStreamer['view_count'],
            $this->FakeStreamer['created_at']
        );
    }
}
