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
}
