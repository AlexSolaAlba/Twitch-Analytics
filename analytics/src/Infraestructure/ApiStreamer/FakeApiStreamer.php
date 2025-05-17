<?php

namespace TwitchAnalytics\Infraestructure\ApiStreamer;

use TwitchAnalytics\Domain\Models\Streamer;

class FakeApiStreamer implements ApiStreamerInterface
{
    private array $FakeStreamer = [
        "id" => "1",
        "login" => "elsmurfoz",
        "display_name" => "elsmurfoz",
        "type" => "",
        "broadcaster_type" => "",
        "description" => "",
        "profile_image_url" => "https://static-cdn.jtvnw.net/user-default-pictures-uv/998f01ae-def8-11e9-b95c-784f43822e80-profile_image-300x300.png",
        "offline_image_url" => "",
        "view_count" => "0",
        "created_at" => "2007-05-22T10:37:47Z"
    ];
    public function getStreamerFromTwitch($userId): Streamer
    {
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
