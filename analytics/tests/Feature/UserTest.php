<?php

namespace Feature;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

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
        $response = $this->get('/analytics/user?id=1', [
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
        $response = $this->get('/analytics/user?id=1', [
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
    public function gets400WhenIdIsWrong()
    {
        $response = $this->get('/analytics/user?id=s', [
            'Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(400);
        $response->seeJson(
            [
                'error' => 'Invalid or missing id parameter.'
            ]
        );
    }

    /**
     * @test
     */
    public function gets401WhenTokenNotExists()
    {
        $response = $this->get('/analytics/user?id=1', [
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
        $response = $this->get('/analytics/user?id=1', [
            'Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
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
    public function gets200WhenStreamerIdExistsInDB()
    {
        $response = $this->get('/analytics/user?id=1', ['Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6']);

        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'id' => '1',
                'login' => 'elsmurfoz',
                'display_name' => 'elsmurfoz',
                'type' => '',
                'broadcaster_type' => '',
                'description' => '',
                'profile_image_url' => 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png',
                'offline_image_url' => '',
                'view_count' => '0',
                'created_at' => '2007-05-22T10:37:47Z',
            ]
        );
    }

    /**
     * @test
     */
    public function gets200WhenStreamerIdNotExistsInDBAndIsInTheApi()
    {
        $response = $this->get('/analytics/user?id=4', ['Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6']);

        $dataBaseHandler = new DataBaseHandler();
        $dataBaseHandler->deleteTestStreamerFromDB();

        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'id' => '4',
                'login' => 'elsmurfoz',
                'display_name' => 'elsmurfoz',
                'type' => '',
                'broadcaster_type' => '',
                'description' => '',
                'profile_image_url' => 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png',
                'offline_image_url' => '',
                'view_count' => '0',
                'created_at' => '2007-05-22T10:37:47Z',
            ]
        );
    }
}
