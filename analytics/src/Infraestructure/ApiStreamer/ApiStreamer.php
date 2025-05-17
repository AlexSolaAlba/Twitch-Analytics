<?php

namespace TwitchAnalytics\Infraestructure\ApiStreamer;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Infraestructure\ApiStreamer\ApiStreamerInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Exceptions\NotFoundException;

class ApiStreamer implements ApiStreamerInterface
{
    public function getStreamerFromTwitch(int $streamerId, string $accessToken): Streamer
    {
        $curl = curl_init();
        $clientID = env('CLIENT_ID');
        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$streamerId");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Client-ID: $clientID",
            "Authorization: Bearer $accessToken"
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $responseData = json_decode($response, true);

        switch ($httpCode) {
            case 200:
                if (isset($responseData['data'][0])) {
                    return new Streamer(
                        $responseData['data'][0]['id'],
                        $responseData['data'][0]['login'],
                        $responseData['data'][0]['display_name'],
                        $responseData['data'][0]['type'],
                        $responseData['data'][0]['broadcaster_type'],
                        $responseData['data'][0]['description'],
                        $responseData['data'][0]['profile_image_url'],
                        $responseData['data'][0]['offline_image_url'],
                        $responseData['data'][0]['view_count'],
                        $responseData['data'][0]['created_at']
                    );
                }
                throw new NotFoundException('User not found.');
            case 404:
                throw new NotFoundException('User not found.');
        }
        throw new TwitchApiException('User not found.');
    }
}
