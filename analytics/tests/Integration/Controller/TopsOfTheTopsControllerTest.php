<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Application\Services\TopsOfTheTopsService;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsController;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsValidator;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\FakeApiTwitchVideos;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandlerVideos;
use TwitchAnalytics\Infraestructure\Repositories\TopsOfTheTopsRepository;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class TopsOfTheTopsControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private TopsOfTheTopsController $topsController;
    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DataBaseHandlerVideos();
        $fakeApiTwitchClient = new FakeApiTwitchToken();
        $twitchUserRepository = new TwitchUserRepository($this->dataBaseHandler, $fakeApiTwitchClient);
        $timeProvider = new SystemTimeProvider();
        $refreshTwitchToken = new RefreshTwitchTokenService($twitchUserRepository, $timeProvider);
        $userValidator = new UserValidator();
        $topsValidator = new TopsOfTheTopsValidator();
        $userRepository = new UserRepository($this->dataBaseHandler);
        $apiVideos = new FakeApiTwitchVideos();
        $topsRepository = new TopsOfTheTopsRepository($apiVideos, $this->dataBaseHandler);
        $topsService = new TopsOfTheTopsService($topsRepository);
        $this->topsController = new TopsOfTheTopsController($refreshTwitchToken, $userValidator, $topsValidator, $userRepository, $topsService);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongTokenReturnsAnException()
    {
        $request = Request::create('/topsofthetops', 'GET', [
            'since' => 50,
        ], [], [], [
            'HTTP_Authorization' => 'Bear',
        ]);

        $response = $this->topsController->__invoke($request);

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
        $request = Request::create('/topsofthetops', 'GET', [
            'since' => 50,
        ], [], [], [
            'HTTP_Authorization' => '',
        ]);

        $response = $this->topsController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongSinceReturnsAnException()
    {
        $request = Request::create('/topsofthetops', 'GET', [
            'since' => 'a',
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->topsController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Invalid since parameter.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatNotExistsReturnsAnException()
    {
        $request = Request::create('/topsofthetops', 'GET', [
            'since' => 50,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->topsController->__invoke($request);

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
        $request = Request::create('/topsofthetops', 'GET', [
            'since' => 50,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);

        $response = $this->topsController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenGoodSinceAndTokenReturnsTopsInfo()
    {
        $request = Request::create('/topsofthetops', 'GET', [
            'since' => 50,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response = $this->topsController->__invoke($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
        [
            'game_id' => 509658,
            'game_name' => 'Just Chatting',
            'user_name' => 'Kai Cenat',
            'total_videos' => 36,
            'total_views' => 414857711,
            'most_viewed_title' => '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
            'most_viewed_views' => 24868821,
            'most_viewed_duration' => '22h5m32s',
            'most_viewed_created_at' => '2024-11-28T02:06:07Z'
        ],
        [
            'game_id' => 516575,
            'game_name' => 'VALORANT',
            'user_name' => '0',
            'total_videos' => 3,
            'total_views' => 7705399,
            'most_viewed_title' => 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew!',
            'most_viewed_views' => 4549587,
            'most_viewed_duration' => '15h15m21s',
            'most_viewed_created_at' => '2020-09-11T18:52:09Z'
        ]
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenNoSinceAndGoodTokenReturnsTopsInfo()
    {
        $request = Request::create('/topsofthetops', 'GET', [
        ], [], [], [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response = $this->topsController->__invoke($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            [
                'game_id' => 509658,
                'game_name' => 'Just Chatting',
                'user_name' => 'Kai Cenat',
                'total_videos' => 36,
                'total_views' => 414857711,
                'most_viewed_title' => '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
                'most_viewed_views' => 24868821,
                'most_viewed_duration' => '22h5m32s',
                'most_viewed_created_at' => '2024-11-28T02:06:07Z'
            ],
            [
                'game_id' => 516575,
                'game_name' => 'VALORANT',
                'user_name' => '0',
                'total_videos' => 3,
                'total_views' => 7705399,
                'most_viewed_title' => 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew!',
                'most_viewed_views' => 4549587,
                'most_viewed_duration' => '15h15m21s',
                'most_viewed_created_at' => '2020-09-11T18:52:09Z'
            ]
        ], $response->getData(true));
    }
}
