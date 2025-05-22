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

        $streams[] = new Stream(
            'Jugando Elden Ring con mods',
            'GamerPro88'
        );

        $streams[] = new Stream(
            'Diseñando en Figma en vivo',
            'UXLaura'
        );

        return $streams;
    }
}
