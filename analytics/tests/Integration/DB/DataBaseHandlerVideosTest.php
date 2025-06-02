<?php

namespace Integration\DB;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandlerVideos;
use TwitchAnalytics\Domain\Models\Video;

class DataBaseHandlerVideosTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DataBaseHandlerVideos();
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getVideosFromDBGetsVideos()
    {
        $videosFake = [
            new Video(
                509658,
                'Just Chatting',
                'Kai Cenat',
                36,
                414857711,
                '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL',
                24868821,
                '22h5m32s',
                '2024-11-28T02:06:07Z'
            ), new Video(
                516575,
                'VALORANT',
                '0',
                3,
                7705399,
                'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew!',
                4549587,
                '15h15m21s',
                '2020-09-11T18:52:09Z'
            )];
        $videos = $this->dataBaseHandler->getVideosFromDB();
        $videos[0]->setCreatedAt(null);
        $videos[1]->setCreatedAt(null);
        $this->assertEquals($videos, $videosFake);
    }
}
