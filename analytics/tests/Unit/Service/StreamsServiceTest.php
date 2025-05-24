<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Application\Services\StreamsService;
use TwitchAnalytics\Domain\Models\Stream;
use TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\FakeApiTwitchStreams;
use TwitchAnalytics\Infraestructure\Repositories\StreamsRepository;

class StreamsServiceTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

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
