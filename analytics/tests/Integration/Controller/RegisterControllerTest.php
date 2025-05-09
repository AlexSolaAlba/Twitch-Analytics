<?php

namespace Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\Register\RegisterController;
use TwitchAnalytics\Controllers\Register\RegisterValidator;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

class RegisterControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private RegisterController $registerController;
    private RegisterService $registerService;
    private RegisterValidator $registerValidator;
    private RandomKeyGenerator $keyGenerator;

    /**
     * @throws RandomException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->keyGenerator = Mockery::mock(RandomKeyGenerator::class);
        $this->keyGenerator->allows()->generateRandomKey()->andReturns("24e9a3dea44346393f632e4161bc83e6");
        $this->registerService = new RegisterService($this->keyGenerator);
        $this->registerValidator = new RegisterValidator();
        $this->registerController = new RegisterController($this->registerService, $this->registerValidator);
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
