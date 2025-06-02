<?php

namespace TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Models\Video;

class ApiTwitchVideos implements ApiTwitchVideosInterface
{
    public function getVideosFromTwitch($accessToken): array
    {
        $curl = curl_init();
        $clientID = env('CLIENT_ID');
        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/games/top?first=3");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Client-ID: $clientID",
            "Authorization: Bearer $accessToken"
        ]);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response_data = json_decode($response, true);

        $filtered_videos = [];
        if (isset($response_data["data"])) {
            foreach ($response_data["data"] as $game) {
                $gameId = $game["id"];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/videos?game_id=$gameId&first=40&sort=views");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    "Client-ID: $clientID",
                    "Authorization: Bearer $accessToken"
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
                $response_data2 = json_decode($response, true);
                $videoCount = 0;
                $viewCount = 0;
                foreach ($response_data2["data"] as $video) {
                    if ($video["user_name"] == $response_data2["data"][0]["user_name"]) {
                        $videoCount++;
                        $viewCount += $video["view_count"];
                    }
                }
                $filtered_videos[] = new Video(
                    $game["id"],
                    $game["name"],
                    $response_data2["data"][0]["user_name"],
                    $videoCount,
                    $viewCount,
                    $response_data2["data"][0]["title"],
                    $response_data2["data"][0]["view_count"],
                    $response_data2["data"][0]["duration"],
                    $response_data2["data"][0]["created_at"]
                );
            }
            return $filtered_videos;
        }
        throw new TwitchApiException("Internal server error.");
    }
}
