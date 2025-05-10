<?php

namespace Integration\Controller;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Random\RandomException;
use TwitchAnalytics\Controllers\Token\TokenController;
use TwitchAnalytics\Controllers\Token\TokenValidator;
use TwitchAnalytics\Domain\DB\DataBaseHandler;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

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
        $tokenValidator = new TokenValidator();
        $this->tokenController = new TokenController($dataBaseHandler, $tokenValidator, $keyGenerator);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets400WhenEmailParameterIsMissing(): void
    {
        $request = Request::create('/register', 'POST');

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
        $request = Request::create('/register', 'POST', [
            'email' => 'testexample.com'
        ]);

        $response = $this->tokenController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'The email must be a valid email address'
        ], $response->getData(true));
    }
}
