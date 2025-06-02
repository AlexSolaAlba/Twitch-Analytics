<?php

namespace Feature;

use Laravel\Lumen\Testing\TestCase;

class TokenTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * @test
     */
    public function gets400WhenEmailParameterIsMissing()
    {
        $response = $this->post('/token');

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'The email is mandatory'
            ]
        );
    }


}
