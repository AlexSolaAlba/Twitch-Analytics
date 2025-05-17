<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Application\Services\UserService;
use TwitchAnalytics\Controllers\User\UserController;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\FakeApiTwitchClient;
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
        $fakeApiTwitchClient = new FakeApiTwitchClient();
        $twitchUserRepository = new TwitchUserRepository($dataBaseHandler, $fakeApiTwitchClient);
        $timeProvider = new SystemTimeProvider();
        $refreshTwitchToken = new RefreshTwitchTokenService($twitchUserRepository, $timeProvider);
        $userValidator = new UserValidator();
        $userRepository = new UserRepository($dataBaseHandler);
        $apiStreamer = new ApiStreamer();
        $userService = new UserService($dataBaseHandler, $apiStreamer);
        $this->userController = new UserController($refreshTwitchToken, $userValidator, $userRepository, $userService);
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

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function notGivenTokenReturnsAnException()
    {
        $request = Request::create('/user', 'GET', [
            'id' => 1,
        ], [], [], [
            'HTTP_Authorization' => '',
        ]);

        $response = $this->userController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongIdReturnsAnException()
    {
        $request = Request::create('/user', 'GET', [
            'id' => 'a',
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->userController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Invalid or missing id parameter.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatNotExistsReturnsAnException()
    {
        $request = Request::create('/user', 'GET', [
            'id' => 1,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->userController->__invoke($request);

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
        $request = Request::create('/user', 'GET', [
            'id' => 1,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);

        $response = $this->userController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenStreamerIdThatExistsInDBReturnsStreamerInfo()
    {
        $request = Request::create('/user', 'GET', [
            'id' => 1,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response = $this->userController->__invoke($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'id' => '1',
            'login' => 'elsmurfoz',
            'display_name' => 'elsmurfoz',
            'type' => '',
            'broadcaster_type' => '',
            'description' => '',
            'profile_image_url' => 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png',
            'offline_image_url' => '',
            'view_count' => '0',
            'created_at' => '2007-05-22T10:37:47Z',
        ], $response->getData(true));
    }
}
