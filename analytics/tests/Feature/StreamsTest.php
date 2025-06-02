<?php

namespace TwitchAnalytics\Tests\Feature;

use Laravel\Lumen\Testing\TestCase;

class StreamsTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * @test
     */
    public function givenWrongTokenReturnsAnException(): void
    {
        $response = $this->get('/analytics/streams', [
                'Authorization' => 'Bear',
            ]);
        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }
    /**
     * @test
     */
    public function notGivenTokenReturnsAnException(): void
    {
        $response = $this->get('/analytics/streams', [
            'Authorization' => '',
        ]);
        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }
    /**
     * @test
     */
    public function givenTokenThatNotExistsReturnsAnException(): void
    {
        $response = $this->get('/analytics/streams', [
            'Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }
    /**
     * @test
     */
    public function givenTokenThatExistsButIsExpiredReturnsAnException(): void
    {
        $response = $this->get('/analytics/streams', [
            'Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);
        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }
    /**
     * @test
     */
    public function get200WhenTokenIsValid()
    {
        $response = $this->get('/analytics/streams', [
            'Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);
        $response->assertResponseStatus(200);
        $response->seeJson([
            'title' => 'Explorando el universo en vivo',
            'user_name' => 'AstroNico'
        ]);

        $response->seeJson([
            'title' => 'Cocinando con estilo',
            'user_name' => 'ChefLaura'
        ]);


    }
}
