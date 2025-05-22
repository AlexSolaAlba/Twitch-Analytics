<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Models\Stream;

class ApiTwitchStreams implements ApiTwitchStreamsInterface
{
    public function getStreamsFromTwitch($accessToken): array
    {
        $curl = curl_init();
        $clientID = env('CLIENT_ID');
        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/streams?first=100");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Client-ID: $clientID",
            "Authorization: Bearer $accessToken"
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);

        $streams = [];

        if (isset($response_data['data'])) {
            foreach ($response_data['data'] as $stream) {
                $streams[] = new Stream(
                    $stream['title'],
                    $stream['user_name']
                );
            }
            return $streams;
        }
        throw new TwitchApiException('Internal server error.');
    }
}
