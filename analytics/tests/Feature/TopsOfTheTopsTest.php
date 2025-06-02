<?php

namespace Feature;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;

class TopsOfTheTopsTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongTokenReturnsAnException()
    {
        $response = $this->get('/analytics/topsofthetops?since=50', [
        'HTTP_Authorization' => 'Bear',
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function notGivenTokenReturnsAnException()
    {
        $response = $this->get('/analytics/topsofthetops?since=50', [
            'HTTP_Authorization' => ''
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongSinceReturnsAnException()
    {
        $response = $this->get('/analytics/topsofthetops?since=a', [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(400);
        $response->seeJson([
            'error' => 'Invalid since parameter.'
        ]);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatNotExistsReturnsAnException()
    {
        $response = $this->get('/analytics/topsofthetops?since=50', [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatExistsButIsExpiredReturnsAnException()
    {
        $response = $this->get('/analytics/topsofthetops?since=50', [
            'HTTP_Authorization' => 'Bearer e9cb15bba53c9d05a23c21afc7b44f40',
        ]);

        $response->assertResponseStatus(401);
        $response->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenGoodSinceAndTokenReturnsTopsInfo()
    {
        $response = $this->get('/analytics/topsofthetops?since=50', [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonEquals([
            [
                'game_id' => 509658,
                'game_name' => 'Just Chatting',
                'user_name' => 'Kai Cenat',
                'total_videos' => 36,
                'total_views' => 414857711,
                'most_viewed_title' => '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
                'most_viewed_views' => 24868821,
                'most_viewed_duration' => '22h5m32s',
                'most_viewed_created_at' => '2024-11-28T02:06:07Z',
            ],
            [
                'game_id' => 516575,
                'game_name' => 'VALORANT',
                'user_name' => '0',
                'total_videos' => 3,
                'total_views' => 7705399,
                'most_viewed_title' => 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew!',
                'most_viewed_views' => 4549587,
                'most_viewed_duration' => '15h15m21s',
                'most_viewed_created_at' => '2020-09-11T18:52:09Z'
            ]
        ]);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenNoSinceAndGoodTokenReturnsTopsInfo()
    {
        $response = $this->get('/analytics/topsofthetops', [
            'HTTP_Authorization' => 'Bearer 24e9a3dea44346393f632e4161bc83e6',
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonEquals([
            [
                'game_id' => 509658,
                'game_name' => 'Just Chatting',
                'user_name' => 'Kai Cenat',
                'total_videos' => 36,
                'total_views' => 414857711,
                'most_viewed_title' => '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
                'most_viewed_views' => 24868821,
                'most_viewed_duration' => '22h5m32s',
                'most_viewed_created_at' => '2024-11-28T02:06:07Z',
            ],
            [
                'game_id' => 516575,
                'game_name' => 'VALORANT',
                'user_name' => '0',
                'total_videos' => 3,
                'total_views' => 7705399,
                'most_viewed_title' => 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew!',
                'most_viewed_views' => 4549587,
                'most_viewed_duration' => '15h15m21s',
                'most_viewed_created_at' => '2020-09-11T18:52:09Z'
            ]
        ]);
    }
}
