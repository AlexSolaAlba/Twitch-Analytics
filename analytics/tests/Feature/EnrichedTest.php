<?php

namespace TwitchAnalytics\Tests\Feature;

use Laravel\Lumen\Testing\TestCase;

class EnrichedTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * @test
     */
    public function gets401WhenTokenIsWrong(): void
    {
        $response = $this->get('/analytics/streams/enriched?limit=2', [
            'Authorization' => 'Bear',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson(
            [
                'error' => 'Unauthorized. Token is invalid or expired.'
            ]
        );
    }

    /**
     * @test
     */
    public function gets401WhenTokenIsNotGiven()
    {
        $response = $this->get('/analytics/streams/enriched?limit=2', [
            'Authorization' => '',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson(
            [
                'error' => 'Unauthorized. Token is invalid or expired.'
            ]
        );
    }

    /**
     * @test
     */
    public function gets400WhenLimitIsWrong()
    {
        $response = $this->get('/analytics/streams/enriched?limit=a', [
            'Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'Invalid or missing limit parameter.'
            ]
        );
    }

    /**
     * @test
     */
    public function gets401WhenTokenNotExists()
    {
        $response = $this->get('/analytics/streams/enriched?limit=2', [
            'Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson(
            [
                'error' => 'Unauthorized. Token is invalid or expired.'
            ]
        );
    }

    /**
     * @test
     */
    public function gets401WhenTokenExistsButIsExpired()
    {
        $response = $this->get('/analytics/streams/enriched?limit=2', [
            'Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson(
            [
                'error' => 'Unauthorized. Token is invalid or expired.'
            ]
        );
    }
}
