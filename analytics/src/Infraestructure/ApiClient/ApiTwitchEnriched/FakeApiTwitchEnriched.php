<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched;

use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Models\EnrichedStream;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class FakeApiTwitchEnriched implements ApiTwitchEnrichedInterface
{
    private array $FakeStream = [
        [
            'streamerId' => '1',
            'userId' => '1001',
            'userName' => 'TechGuru',
            'viewerCount' => '1500',
            'userDisplayName' => 'TechGuruLive',
            'title' => 'Desarrollando apps con Laravel en vivo',
            'profileImageUrl' => 'https://example.com/images/techguru.jpg'
        ],
        [
            'streamerId' => '2',
            'userId' => '1002',
            'userName' => 'MusicLover',
            'viewerCount' => '900',
            'userDisplayName' => 'TheMusicLover',
            'title' => 'Sesión chill en piano',
            'profileImageUrl' => 'https://example.com/images/musiclover.jpg'
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
                $stream['streamerId'],
                $stream['userId'],
                $stream['userName'],
                $stream['viewerCount'],
                $stream['userDisplayName'],
                $stream['title'],
                $stream['profileImageUrl']
            );
        }
        return $streams;
    }
}
