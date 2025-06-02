<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Application\Services\TopsOfTheTopsService;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\FakeApiTwitchVideos;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandlerVideos;
use TwitchAnalytics\Infraestructure\Repositories\TopsOfTheTopsRepository;

class TopsOfTheTopsServiceTest extends TestCase
{
    private TopsOfTheTopsService $topsService;
    private DataBaseHandlerVideos $dataBaseHandler;
    protected function setUp(): void
    {
        parent::setUp();
        $apiVideos = new FakeApiTwitchVideos();
        $this->dataBaseHandler = new DataBaseHandlerVideos();
        $topsRepository = new TopsOfTheTopsRepository($apiVideos, $this->dataBaseHandler);
        $this->topsService = new TopsOfTheTopsService($topsRepository);
    }
    /**
     * @test
     */
    public function givenSinceReturnsTopsOfTheTopsInfo(): void
    {
        $response = $this->topsService->returnVideosInfo("24e9a3dea44346393f632e4161bc83e6", 50);

        $this->assertEquals([
            [
                'game_id' => 509658,
                'game_name' => 'Just Chatting',
                'user_name' => 'Kai Cenat',
                'total_videos' => 36,
                'total_views' => 414857711,
                'most_viewed_title' => '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
                'most_viewed_views' => 24868821,
                'most_viewed_duration' => '22h5m32s',
                'most_viewed_created_at' => '2024-11-28T02:06:07Z'
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
        ], $response);
    }

    /**
     * @test
     */
    public function notGivenSinceReturnsTopsOfTheTopsInfo(): void
    {
        $response = $this->topsService->returnVideosInfo("24e9a3dea44346393f632e4161bc83e6", null);

        $this->assertEquals([
            [
                'game_id' => 509658,
                'game_name' => 'Just Chatting',
                'user_name' => 'Kai Cenat',
                'total_videos' => 36,
                'total_views' => 414857711,
                'most_viewed_title' => '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
                'most_viewed_views' => 24868821,
                'most_viewed_duration' => '22h5m32s',
                'most_viewed_created_at' => '2024-11-28T02:06:07Z'
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
        ], $response);
    }
}
