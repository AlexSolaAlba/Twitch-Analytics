<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Application\Services\EnrichedService;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\Enriched\EnrichedController;
use TwitchAnalytics\Controllers\Enriched\EnrichedValidator;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Domain\Repositories\EnrichedRepositoryInterface;
use TwitchAnalytics\Domain\Models\EnrichedStream;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\FakeApiTwitchEnriched;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\EnrichedRepository;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class EnrichedServiceTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    private EnrichedService $enrichedService;

    protected function setUp(): void
    {
        parent::setUp();
        $apiStreams = new FakeApiTwitchEnriched();
        $enrichedRepository = new EnrichedRepository($apiStreams);
        $this->enrichedService = new EnrichedService($enrichedRepository);
    }

    /**
     * @test
     */
    public function givenLimitAccessTokenReturnEnrichedStreamsInfo(): void
    {
        $result = $this->enrichedService->returnEnrichedStreamsInfo(2, '24e9a3dea44346393f632e4161bc83e6');
        $this->assertEquals([
            [
                'stream_id' => '1',
                'user_id' => '1001',
                'user_name' => 'TechGuru',
                'viewer_count' => '1500',
                'user_display_name' => 'TechGuruLive',
                'title' => 'Desarrollando apps con Laravel en vivo',
                'profile_image_url' => 'https://example.com/images/techguru.jpg'
            ],
            [
                'stream_id' => '2',
                'user_id' => '1002',
                'user_name' => 'MusicLover',
                'viewer_count' => '900',
                'user_display_name' => 'TheMusicLover',
                'title' => 'Sesión chill en piano',
                'profile_image_url' => 'https://example.com/images/musiclover.jpg'
            ]
        ], $result);
    }
}
