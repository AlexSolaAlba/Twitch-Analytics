<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Models\EnrichedStream;

class ApiTwitchEnriched implements ApiTwitchEnrichedInterface
{
    public function getEnrichedStreamsFromTwitch(int $limit, string $accessToken): array
    {
        $curl = curl_init();
        $clientID = env('CLIENT_ID');
        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/streams?first=$limit");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Client-ID: $clientID",
            "Authorization: Bearer $accessToken"
        ]);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response_data = json_decode($response, true);

        $streams = [];
        switch ($http_code) {
            case 200:
                if (isset($response_data['data'])) {
                    foreach ($response_data['data'] as $stream) {
                        $userId = $stream['user_id'];
                        $curl = curl_init();

                        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$userId");
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, [
                            "Client-ID: $clientID",
                            "Authorization: Bearer $accessToken"
                        ]);
                        $response2 = curl_exec($curl);
                        curl_close($curl);
                        $response_data2 = json_decode($response2, true);

                        if (isset($response_data2['data'])) {
                            foreach ($response_data2['data'] as $EnrichedStream) {
                                $streams[] = new EnrichedStream(
                                    $EnrichedStream['id'],
                                    $EnrichedStream['user_id'],
                                    $EnrichedStream['user_name'],
                                    $EnrichedStream['viewer_count'],
                                    $EnrichedStream['user_login'],
                                    $EnrichedStream['title'],
                                    $EnrichedStream['profile_image_url']
                                );
                            }
                        }
                        return $streams;
                    }
                }
                throw new TwitchApiException('Invalid or missing limit parameter.');
            case 400:
                throw new TwitchApiException('Invalid or missing limit parameter.');
            case 500:
                throw new TwitchApiException('Internal server error.');
        }
        throw new ValidationException('Invalid or missing limit parameter.');
    }
}
