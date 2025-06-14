<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Application\Services\StreamsService;
use TwitchAnalytics\Controllers\Streams\StreamsController;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\FakeApiTwitchStreams;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\StreamsRepository;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class StreamsControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    private StreamsController $streamsController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DataBaseHandler();
        $fakeApiTwitchClient = new FakeApiTwitchToken();
        $twitchUserRepository = new TwitchUserRepository($this->dataBaseHandler, $fakeApiTwitchClient);
        $timeProvider = new SystemTimeProvider();
        $refreshTwitchToken = new RefreshTwitchTokenService($twitchUserRepository, $timeProvider);
        $userValidator = new UserValidator();
        $userRepository = new UserRepository($this->dataBaseHandler);
        $apiStreams = new FakeApiTwitchStreams();
        $streamsRepository = new StreamsRepository($apiStreams);
        $streamsService = new StreamsService($streamsRepository);
        $this->streamsController = new StreamsController($refreshTwitchToken, $userValidator, $userRepository, $apiStreams, $streamsService);
    }
    /**
 * @test
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
    public function givenWrongTokenReturnsAnException()
    {
        $request = Request::create('/streams', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bear',
        ]);

        $response = $this->streamsController->__invoke($request);

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
        $request = Request::create('/streams', 'GET', [], [], [], [
            'HTTP_Authorization' => '',
        ]);

        $response = $this->streamsController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatNotExistsReturnsAnException()
    {
        $request = Request::create('/streams', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->streamsController->__invoke($request);

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
        $request = Request::create('/streams', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);

        $response = $this->streamsController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenValidTokenReturnsStreamsInfoFromAPI()
    {
        $request = Request::create('/streams', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response = $this->streamsController->__invoke($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([[
            'title' => 'Explorando el universo en vivo',
            'user_name' => 'AstroNico'
        ],
        [
            'title' => 'Cocinando con estilo',
            'user_name' => 'ChefLaura'
        ]], $response->getData(true));
    }
}
