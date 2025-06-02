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

    /**
     * @test
     */
    public function gets200GivenGoodLimitAndToken()
    {
        $response = $this->get('/analytics/streams/enriched?limit=2', [
            'Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6'
        ]);

        $response->assertResponseStatus(200);
        $response->seeJson([
            'stream_id' => '1',
            'user_id' => '1001',
            'user_name' => 'TechGuru',
            'viewer_count' => '1500',
            'user_display_name' => 'TechGuruLive',
            'title' => 'Desarrollando apps con Laravel en vivo',
            'profile_image_url' => 'https://example.com/images/techguru.jpg'
        ]);

        $response->seeJson([
            'stream_id' => '2',
            'user_id' => '1002',
            'user_name' => 'MusicLover',
            'viewer_count' => '900',
            'user_display_name' => 'TheMusicLover',
            'title' => 'Sesión chill en piano',
            'profile_image_url' => 'https://example.com/images/musiclover.jpg'
        ]);
    }
}
