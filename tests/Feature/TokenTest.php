<?php

namespace Feature;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

class TokenTest extends TestCase
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
        $response = $this->post('/token');

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
    public function gets400WhenEmailParameterIsWrong(): void
    {
        $response = $this->post('/token', [
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
     */
    public function gets400WhenEmailParameterIsRightAndKeyParameterIsMissing(): void
    {
        $response = $this->post('/token', [
            'email' => 'test@example.com'
        ]);

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'The api_key is mandatory'
            ]
        );
    }

    /**
     * @test
     */
    public function gets401WhenEmailParameterIsRightAndKeyParameterIsWrong(): void
    {
        $response = $this->post('/token', [
            'email' => 'test@example.com',
            'api_key' => '21343fse'
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson(
            [
                'error' => 'Unauthorized. API access token is invalid.'
            ]
        );
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws RandomException
     */
    public function gets200WhenEmailParameterIsRightAndKeyParameterIsRight()
    {
        $keyGenerator = Mockery::mock(RandomKeyGenerator::class);
        $keyGenerator->allows()->generateRandomKey()->andReturns("24e9a3dea44346393f632e4161bc83e6");
        $this->app->instance(RandomKeyGenerator::class, $keyGenerator);

        $response = $this->post('/token', [
            'email' => 'test@example.com',
            'api_key' => '24e9a3dea44346393f632e4161bc83e6'
        ]);

        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'token' => "24e9a3dea44346393f632e4161bc83e6"
            ]
        );
    }
}
