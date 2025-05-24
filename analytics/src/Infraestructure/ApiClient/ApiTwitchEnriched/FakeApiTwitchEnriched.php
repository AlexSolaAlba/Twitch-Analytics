<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched;

use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Models\EnrichedStream;

class FakeApiTwitchEnriched implements ApiTwitchEnrichedInterface
{
    private array $FakeStream = [
        [
            'streamer_id' => '1',
            'user_id' => '1001',
            'user_name' => 'TechGuru',
            'viewer_count' => '1500',
            'user_display_name' => 'TechGuruLive',
            'title' => 'Desarrollando apps con Laravel en vivo',
            'profile_image_url' => 'https://example.com/images/techguru.jpg'
        ],
        [
            'streamer_id' => '2',
            'user_id' => '1002',
            'user_name' => 'MusicLover',
            'viewer_count' => '900',
            'user_display_name' => 'TheMusicLover',
            'title' => 'Sesión chill en piano',
            'profile_image_url' => 'https://example.com/images/musiclover.jpg'
        ]
    ];

    public function getEnrichedStreamsFromTwitch($limit, $accessToken): array
    {
        $streams = [];
        if ($limit !== 2) {
            throw new ValidationException('Invalid or missing limit parameter.');
        }

        foreach ($this->FakeStream as $stream) {
            $streams[] = new EnrichedStream(
                $stream['streamer_id'],
                $stream['user_id'],
                $stream['user_name'],
                $stream['viewer_count'],
                $stream['user_display_name'],
                $stream['title'],
                $stream['profile_image_url']
            );
        }
        return $streams;
    }
}
