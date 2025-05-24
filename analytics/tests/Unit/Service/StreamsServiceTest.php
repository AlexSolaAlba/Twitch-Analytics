<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use TwitchAnalytics\Application\Services\StreamsService;
use TwitchAnalytics\Domain\Models\Stream;
use TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface;

class StreamsServiceTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    public function testReturnStreamsInfoReturnsFormattedData()
    {
        $accessToken = 'fake-token';
        $stream1 = new Stream('Explorando el universo en vivo', 'AstroNico');
        $stream2 = new Stream('Cocinando con estilo', 'ChefLaura');

        $repositoryMock = Mockery::mock(StreamsRepositoryInterface::class);
        $repositoryMock
            ->shouldReceive('returnStreamsInfoFromAPI')
            ->once()
            ->with($accessToken)
            ->andReturn([$stream1, $stream2]);
        $service = new StreamsService($repositoryMock);
        $result = $service->returnStreamsInfo($accessToken);
        $this->assertEquals([
            ['title' => 'Explorando el universo en vivo', 'user_name' => 'AstroNico'],
            ['title' => 'Cocinando con estilo', 'user_name' => 'ChefLaura'],
        ], $result);
    }
}
