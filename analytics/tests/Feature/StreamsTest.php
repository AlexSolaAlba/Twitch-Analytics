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
}
