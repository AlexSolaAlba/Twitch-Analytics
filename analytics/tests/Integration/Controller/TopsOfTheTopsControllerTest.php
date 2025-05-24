<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsController;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsValidator;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\FakeApiTwitchVideos;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandlerVideos;
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
        $this->topsController = new TopsOfTheTopsController($refreshTwitchToken, $userValidator, $topsValidator, $userRepository, $apiVideos, $this->dataBaseHandler);
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
}
