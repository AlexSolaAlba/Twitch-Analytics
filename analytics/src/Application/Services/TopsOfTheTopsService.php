<?php

namespace TwitchAnalytics\Application\Services;

use TwitchAnalytics\Domain\Repositories\TopsOfTheTopsRepositoryInterface;

class TopsOfTheTopsService
{
    private TopsOfTheTopsRepositoryInterface $topsRepository;
    public function __construct(TopsOfTheTopsRepositoryInterface $topsRepository)
    {
        $this->topsRepository = $topsRepository;
    }
    public function returnVideosInfo($accessToken, $since): array
    {
        $videos = $this->topsRepository->readCache();
        $currentDate = time();
        try {
            $interval = $currentDate - strtotime($videos[0]->getCreatedAt());
        } catch (\Throwable $ex) {
            $interval = 700;
        }
        if ($interval > 600 || (isset($since) && ($interval < $since))) {
            $this->topsRepository->deleteCache();
            $videos = $this->topsRepository->returnVideosInfoFromAPI($accessToken);
            $this->topsRepository->updateCache($videos);
        }
        return array_map(fn($video) => [
            'game_id' => $video->getGameId(),
            'game_name' => $video->getGameName(),
            'user_name' => $video->getUserName(),
            'total_videos' => $video->getTotalVideos(),
            'total_views' => $video->getTotalViews(),
            'most_viewed_title' => $video->getMostViewedTitle(),
            'most_viewed_views' => $video->getMostViewedViews(),
            'most_viewed_duration' => $video->getMostViewedDuration(),
            'most_viewed_created_at' => $video->getMostViewedCreatedAt()
        ], $videos);
    }
}
