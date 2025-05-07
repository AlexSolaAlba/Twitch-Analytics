<?php

namespace Integration\Controller;

use Illuminate\Http\Request;


use Laravel\Lumen\Testing\TestCase;
use Mockery;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\Register\RegisterController;

class RegisterControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private RegisterController $registerController;
    private RegisterService $registerService;

    /*protected function setUp(): void
    {
        $this->registerService = Mockery::mock(RegisterService::class);

        $this->registerController = new RegisterController($this->registerService);
    }*/

    /**
     * @test
     */
    public function gets400WhenEmailParameterIsMissing(): void
    {
        $this->registerService = new RegisterService;
        $this->registerController = new RegisterController($this->registerService);

        $request = Request::create('/register', 'POST');

        $response = $this->registerController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'The email is mandatory'
        ], $response->getData(true));
    }
}
