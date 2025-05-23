<?php

namespace TwitchAnalytics\Tests\Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\EnrichedService;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\Enriched\EnrichedController;
use TwitchAnalytics\Controllers\Enriched\EnrichedValidator;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\FakeApiTwitchEnriched;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\EnrichedRepository;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class EnrichedControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private EnrichedController $enrichedController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DataBaseHandler();
        $fakeApiTwitchClient = new FakeApiTwitchToken();
        $twitchUserRepository = new TwitchUserRepository($this->dataBaseHandler, $fakeApiTwitchClient);
        $timeProvider = new SystemTimeProvider();
        $refreshTwitchToken = new RefreshTwitchTokenService($twitchUserRepository, $timeProvider);
        $userValidator = new UserValidator();
        $enrichedValidator = new EnrichedValidator();
        $userRepository = new UserRepository($this->dataBaseHandler);
        $apiStreams = new FakeApiTwitchEnriched();
        $enrichedRepository = new EnrichedRepository($apiStreams);
        $enrichedService = new EnrichedService($enrichedRepository);
        $this->enrichedController = new EnrichedController($refreshTwitchToken, $userValidator, $enrichedValidator, $userRepository, $apiStreams, $enrichedService);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongTokenReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => 'Bear',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function notGivenTokenReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => '',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongLimitReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 'a',
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Invalid or missing limit parameter.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatNotExistsReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatExistsButIsExpiredReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenGoodLimitAndTokenReturnsStreamsInfo()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response = $this->enrichedController->__invoke($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            [
                'streamer_id' => '1',
                'user_id' => '1001',
                'user_name' => 'TechGuru',
                'viewer_count' => '1500',
                'user_display_name' => 'TechGuruLive',
                'title' => 'Desarrollando apps con Laravel en vivo',
                'profile_image_url' => 'https://example.com/images/techguru.jpg'
            ],
            [
                'streamer_id' => '2',
                'user_id' => '1002',
                'user_name' => 'MusicLover',
                'viewer_count' => '900',
                'user_display_name' => 'TheMusicLover',
                'title' => 'Sesión chill en piano',
                'profile_image_url' => 'https://example.com/images/musiclover.jpg'
            ]
        ], $response->getData(true));
    }
}
