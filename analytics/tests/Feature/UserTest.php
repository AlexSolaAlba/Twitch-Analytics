<?php

namespace Feature;

use Laravel\Lumen\Testing\TestCase;

class UserTest extends TestCase
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
        $response = $this->get('/analytics/user?id=1', [], [
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
        $response = $this->get('/analytics/user?id=1', [], [
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
    public function gets400WhenIdisWrong()
    {
        $response = $this->get('/analytics/user?id=s', [], [
            'Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'Invalid or missing id parameter.'
            ]
        );
    }
}
