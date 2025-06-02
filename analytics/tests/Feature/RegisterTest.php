<?php

namespace Feature;

use Laravel\Lumen\Testing\TestCase;

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
}
