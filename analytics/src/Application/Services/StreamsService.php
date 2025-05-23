<?php

namespace TwitchAnalytics\Application\Services;

use TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface;

class StreamsService
{
    private StreamsRepositoryInterface $streamsRepository;
    public function __construct(StreamsRepositoryInterface $StreamsRepository)
    {
        $this->streamsRepository = $StreamsRepository;
    }
    public function returnStreamsInfo($accessToken): array
    {
        $streamObjects = $this->streamsRepository->returnStreamsInfoFromAPI($accessToken);

        $streams = array_map(fn($Stream) => [
            'title' => $Stream->getStreamTitle(),
            'user_name' => $Stream->getStreamUserName(),
        ], $streamObjects);
        return $streams;
    }
}
