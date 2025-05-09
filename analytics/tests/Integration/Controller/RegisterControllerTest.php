<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\Register\RegisterController;
use TwitchAnalytics\Controllers\Register\RegisterValidator;

class RegisterControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private RegisterController $registerController;
    private RegisterService $registerService;
    private RegisterValidator $registerValidator;



    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets400WhenEmailParameterIsMissing(): void
    {
        $this->registerService = new RegisterService();
        $this->registerValidator = new RegisterValidator();
        $this->registerController = new RegisterController($this->registerService, $this->registerValidator);

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
        $this->registerService = new RegisterService();
        $this->registerValidator = new RegisterValidator();
        $this->registerController = new RegisterController($this->registerService, $this->registerValidator);

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
        $this->registerService = Mockery::mock(RegisterService::class);
        $this->registerService->allows()->register("test@example.com")->andReturns(['api_key' => "fafs"]);
        $this->registerValidator = new RegisterValidator();
        $this->registerController = new RegisterController($this->registerService, $this->registerValidator);

        $request = Request::create('/register', 'POST', [
            'email' => 'test@example.com'
        ]);

        $response = $this->registerController->__invoke($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['api_key' => "fafs"], $response->getData(true));
    }
}
