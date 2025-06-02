<?php

namespace Feature;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

class RegisterTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * @test
     */
    public function gets400WhenEmailParameterIsMissing(): void
    {
        $response = $this->post('/register');

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'The email is mandatory'
            ]
        );
    }

    /**
     * @test
     */
    public function gets400WhenEmailParameterIsWrong()
    {
        $response = $this->post('/register', [
            'email' => 'testexample.com'
        ]);

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'The email must be a valid email address'
            ]
        );
    }

    /**
     * @test
     * @throws RandomException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function gets200WhenEmailParameterIsRight(): void
    {
        $keyGenerator = Mockery::mock(RandomKeyGenerator::class);
        $keyGenerator->allows()->generateRandomKey()->andReturns("24e9a3dea44346393f632e4161bc83e6");
        $this->app->instance(RandomKeyGenerator::class, $keyGenerator);

        $response = $this->post('/register', [
            'email' => 'test@example.com'
        ]);

        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'api_key' => '24e9a3dea44346393f632e4161bc83e6'
            ]
        );
    }
}
