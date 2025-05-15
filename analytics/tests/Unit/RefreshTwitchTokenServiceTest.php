<?php

namespace TwitchAnalytics\Tests\Unit;

use DateTime;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Infraestructure\ApiClient\FakeApiTwitchClient;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class RefreshTwitchTokenServiceTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DatabaseHandler();
        $this->fakeApiTwitchClient = new FakeApiTwitchClient();
        $this->twitchUserRepository = new TwitchUserRepository($this->dataBaseHandler, $this->fakeApiTwitchClient);
        $this->timeProvider = Mockery::mock(SystemTimeProvider::class);
        $this->refreshTwitchTokenService = new RefreshTwitchTokenService($this->twitchUserRepository, $this->timeProvider);
    }

    public function testReturnsExistingTokenIfNotExpired()
    {
        $expectedAccessToken = 'jostpf5q0puzmxmkba9iyug38kjtg';

        $twitchUser = new TwitchUser(
            1,
            $expectedAccessToken,
            5011271
        );

        $this->timeProvider
            ->shouldReceive('now')
            ->andReturn(501270);

        $result = $this->refreshTwitchTokenService->refreshTwitchToken();

        $this->assertSame($twitchUser->getAccessToken(), $result->getAccessToken());
        $this->assertEquals($expectedAccessToken, $result->getAccessToken());
    }
}
