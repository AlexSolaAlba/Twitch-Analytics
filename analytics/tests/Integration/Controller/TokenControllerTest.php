<?php

namespace TwitchAnalytics\Tests\Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Application\Services\TokenService;
use TwitchAnalytics\Controllers\Token\TokenController;
use TwitchAnalytics\Controllers\Token\TokenValidator;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;

class TokenControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private TokenController $tokenController;

    /**
     * @throws RandomException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        parent::setUp();
        $keyGenerator = Mockery::mock(RandomKeyGenerator::class);
        $keyGenerator->allows()->generateRandomKey()->andReturns("24e9a3dea44346393f632e4161bc83e6");
        $dataBaseHandler = new DatabaseHandler();
        $userRepository = new UserRepository($dataBaseHandler);
        $tokenValidator = new TokenValidator();
        $tokenService = new TokenService($keyGenerator, $userRepository);
        $this->tokenController = new TokenController($tokenValidator, $tokenService);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets400WhenEmailParameterIsMissing(): void
    {
        $request = Request::create('/token', 'POST');

        $response = $this->tokenController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'The email is mandatory'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets400WhenEmailParameterIsWrong(): void
    {
        $request = Request::create('/token', 'POST', [
            'email' => 'testexample.com'
        ]);

        $response = $this->tokenController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'The email must be a valid email address'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets400WhenEmailParameterIsRightAndKeyParameterIsMissing(): void
    {
        $request = Request::create('/token', 'POST', [
            'email' => 'test@example.com'
        ]);

        $response = $this->tokenController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'The api_key is mandatory'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets401WhenEmailParameterIsRightAndKeyParameterIsWrong(): void
    {
        $request = Request::create('/token', 'POST', [
            'email' => 'test@example.com',
            'api_key' => '21343fse'
        ]);

        $response = $this->tokenController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. API access token is invalid.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets200WhenEmailParameterIsRightAndKeyParameterIsRight(): void
    {
        $request = Request::create('/token', 'POST', [
            'email' => 'test@example.com',
            'api_key' => '24e9a3dea44346393f632e4161bc83e6'
        ]);

        $response = $this->tokenController->__invoke($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['token' => "24e9a3dea44346393f632e4161bc83e6"], $response->getData(true));
    }
}
