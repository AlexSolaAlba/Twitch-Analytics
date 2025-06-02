<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Mockery;
use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class RefreshTwitchTokenServiceTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DatabaseHandler();
        $this->fakeApiTwitchClient = new FakeApiTwitchToken();
        $this->twitchUserRepository = new TwitchUserRepository($this->dataBaseHandler, $this->fakeApiTwitchClient);
        $this->timeProvider = Mockery::mock(SystemTimeProvider::class);
        $this->refreshTwitchTokenService = new RefreshTwitchTokenService($this->twitchUserRepository, $this->timeProvider);
    }

    /**
     * @test
     */
    public function returnsExistingTokenIfNotExpired()
    {
        $expectedAccessToken = 'jostpf5q0puzmxmkba9iyug38kjtg';

        $this->timeProvider
            ->shouldReceive('now')
            ->andReturn(0);

        $result = $this->refreshTwitchTokenService->refreshTwitchToken();

        $this->assertSame($expectedAccessToken, $result->getAccessToken());
    }

    /**
     * @test
     */
    public function returnsTwitchApiTokenIfExpired()
    {
        $expectedAccessToken = 'jostpf5q0puzmxmkba9iyug38kjtg';

        $this->timeProvider
            ->shouldReceive('now')
            ->andReturn(1750899999999);

        $result = $this->refreshTwitchTokenService->refreshTwitchToken();

        $this->assertSame($expectedAccessToken, $result->getAccessToken());
    }
}
