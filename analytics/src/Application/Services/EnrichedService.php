<?php

namespace TwitchAnalytics\Application\Services;

use TwitchAnalytics\Domain\Repositories\EnrichedRepositoryInterface;

class EnrichedService
{
    private EnrichedRepositoryInterface $enrichedRepository;
    public function __construct(EnrichedRepositoryInterface $enrichedRepository)
    {
        $this->enrichedRepository = $enrichedRepository;
    }

    public function returnEnrichedStreamsInfo($limit, $accessToken): array
    {
        $streamObjects = $this->enrichedRepository->returnEnrichedStreamInfoFromAPI($limit, $accessToken);
        $streams = array_map(fn($stream) => [
            'streamer_id' => $stream->getStreamerId(),
            'user_id' => $stream->getUserId(),
            'user_name' => $stream->getUserName(),
            'viewer_count' => $stream->getViewerCount(),
            'user_display_name' => $stream->getUserDisplayName(),
            'title' => $stream->getTitle(),
            'profile_image_url' => $stream->getProfileImageUrl(),
        ], $streamObjects);
        return $streams;
    }
}
