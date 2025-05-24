<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;
use TwitchAnalytics\Domain\Models\EnrichedStream;

class ApiTwitchEnriched implements ApiTwitchEnrichedInterface
{
    public function getEnrichedStreamsFromTwitch(int $limit, string $accessToken): array
    {
        $clientID = env('CLIENT_ID');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.twitch.tv/helix/streams?first={$limit}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Client-ID: {$clientID}",
                "Authorization: Bearer tb9rmjd4bo2ldoru98wogmfaclvrng",
            ],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $data = json_decode($response, true)['data'] ?? [];
        switch ($httpCode) {
            case 200:
                $streams = [];
                foreach ($data as $stream) {
                    $streamId    = $stream['id'];
                    $userId      = $stream['user_id'];
                    $userName    = $stream['user_name'];
                    $viewerCount = $stream['viewer_count'];
                    $title       = $stream['title'];

                    $curl2 = curl_init();
                    curl_setopt_array($curl2, [
                        CURLOPT_URL => "https://api.twitch.tv/helix/users?id={$userId}",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => [
                            "Client-ID: {$clientID}",
                            "Authorization: Bearer tb9rmjd4bo2ldoru98wogmfaclvrng",
                        ],
                    ]);
                    $resp2 = curl_exec($curl2);
                    curl_close($curl2);

                    $userData = json_decode($resp2, true)['data'][0] ?? null;
                    $profileImage = $userData['profile_image_url'] ?? "";

                    $streams[] = new EnrichedStream(
                        $streamId,
                        $userId,
                        $userName,
                        $viewerCount,
                        $userName,
                        $title,
                        $profileImage
                    );
                }
                return $streams;
            case 400:
                throw new TwitchApiException('Invalid or missing limit parameter.');
            case 401:
                throw new TwitchApiException('Unauthorized. Twitch access token is invalid or has expired.');
            case 500:
                throw new TwitchApiException('Internal server error.');
            default:
                throw new ValidationException('Invalid or missing limit parameter.');
        }
    }
}
