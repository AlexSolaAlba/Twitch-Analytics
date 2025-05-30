<?php

namespace TwitchAnalytics\Tests\Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\Register\RegisterController;
use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;

class RegisterControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private RegisterController $registerController;


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
        $registerService = new RegisterService($keyGenerator, $userRepository);
        $validator = new Validator();
        $this->registerController = new RegisterController($registerService, $validator);
    }


    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets400WhenEmailParameterIsMissing(): void
    {
        $request = Request::create('/register', 'POST');

        $response = $this->registerController->__invoke($request);

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
        $request = Request::create('/register', 'POST', [
            'email' => 'testexample.com'
        ]);

        $response = $this->registerController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'The email must be a valid email address'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets200WhenEmailParameterIsRight(): void
    {
        $request = Request::create('/register', 'POST', [
            'email' => 'test@example.com'
        ]);

        $response = $this->registerController->__invoke($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['api_key' => "24e9a3dea44346393f632e4161bc83e6"], $response->getData(true));
    }
}
