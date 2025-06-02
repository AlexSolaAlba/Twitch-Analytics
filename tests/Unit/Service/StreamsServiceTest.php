<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Application\Services\StreamsService;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\FakeApiTwitchStreams;
use TwitchAnalytics\Infraestructure\Repositories\StreamsRepository;

class StreamsServiceTest extends TestCase
{
    private StreamsService $streamsService;

    protected function setUp(): void
    {
        parent::setUp();
        $apiStreams = new FakeApiTwitchStreams();
        $streamsRepository = new StreamsRepository($apiStreams);
        $this->streamsService = new StreamsService($streamsRepository);
    }
    /**
     * @test
     */
    public function returnStreamsInfoReturnsFormattedData()
    {
        $result = $this->streamsService->returnStreamsInfo("24e9a3dea44346393f632e4161bc83e6");
        $this->assertEquals([
            [
                'title' => 'Explorando el universo en vivo',
                'user_name' => 'AstroNico'
            ],
            [
                'title' => 'Cocinando con estilo',
                'user_name' => 'ChefLaura'
            ]
        ], $result);
    }
}
