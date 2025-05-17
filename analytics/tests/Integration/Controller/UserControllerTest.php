<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\User\UserController;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchClient;
use TwitchAnalytics\Infraestructure\ApiStreamer\ApiStreamer;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class UserControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    private UserController $userController;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        parent::setUp();
        $dataBaseHandler = new DataBaseHandler();
        $apiTwitchClient = new ApiTwitchClient();
        $twitchUserRepository = new TwitchUserRepository($dataBaseHandler, $apiTwitchClient);
        $timeProvider = new SystemTimeProvider();
        $refreshTwitchToken = new RefreshTwitchTokenService($twitchUserRepository, $timeProvider);
        $userValidator = new UserValidator();
        $userRepository = new UserRepository($dataBaseHandler);
        $apiStreamer = new ApiStreamer();
        $this->userController = new UserController($refreshTwitchToken, $userValidator, $userRepository, $dataBaseHandler, $apiStreamer);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongTokenReturnsAnException()
    {
        $request = Request::create('/user', 'GET', [
            'id' => 1,
        ], [], [], [
            'HTTP_Authorization' => 'Bear',
        ]);

        $response = $this->userController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
}
