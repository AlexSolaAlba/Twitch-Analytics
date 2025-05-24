<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos;

use TwitchAnalytics\Domain\Models\Video;

class FakeApiTwitchVideos implements ApiTwitchVideosInterface
{
    private array $fakeVideos = [
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
    ];

    public function getVideosFromTwitch($accessToken): array
    {
        $videos = [];
        foreach ($this->fakeVideos as $video) {
            $videos[] = new Video(
                $video["game_id"],
                $video["game_name"],
                $video["user_name"],
                $video["total_videos"],
                $video["total_views"],
                $video["most_viewed_title"],
                $video["most_viewed_views"],
                $video["most_viewed_duration"],
                $video["most_viewed_created_at"]
            );
        }
        return $videos;
    }
}
