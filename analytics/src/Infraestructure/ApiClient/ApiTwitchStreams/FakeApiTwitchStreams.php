<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams;

use TwitchAnalytics\Domain\Models\Stream;

class FakeApiTwitchStreams implements ApiTwitchStreamsInterface
{
    private array $FakeStreams = [
        [
            'title' => 'Explorando el universo en vivo',
            'userName' => 'AstroNico'
        ],
        [
            'title' => 'Cocinando con estilo',
            'userName' => 'ChefLaura'
        ]
    ];

    public function getStreamsFromTwitch($accessToken): array
    {
        $streams = [];

        foreach ($this->FakeStreams as $streamData) {
            $streams[] = new Stream(
                $streamData['title'],
                $streamData['userName']
            );
        }

        return $streams;
    }
}
