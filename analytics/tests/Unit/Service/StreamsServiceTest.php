<?php

namespace Tests\Application\Services;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Application\Services\StreamsService;
use TwitchAnalytics\Domain\Models\Stream;
use TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface;

class StreamsServiceTest extends TestCase
{
    public function testReturnStreamsInfoReturnsFormattedData()
    {
        $accessToken = 'fake-token';
        $stream1 = new Stream('Explorando el universo en vivo', 'AstroNico');
        $stream2 = new Stream('Cocinando con estilo', 'ChefLaura');

        $repositoryMock = $this->createMock(StreamsRepositoryInterface::class);
        $repositoryMock->method('returnStreamsInfoFromAPI')
            ->with($accessToken)
            ->willReturn([$stream1, $stream2]);

        $service = new StreamsService($repositoryMock);

        // Act
        $result = $service->returnStreamsInfo($accessToken);

        $this->assertEquals([
            ['title' => 'Explorando el universo en vivo', 'user_name' => 'AstroNico'],
            ['title' => 'Cocinando con estilo', 'user_name' => 'ChefLaura'],
        ], $result);
    }
}
