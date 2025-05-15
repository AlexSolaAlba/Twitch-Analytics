<?php

namespace TwitchAnalytics\Infraestructure\ApiClient;

use TwitchAnalytics\Domain\Exceptions\TwitchApiException;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Infraestructure\DB\DBException;

class ApiTwitchClient implements ApiTwitchClientInterface
{
    public function getTwitchAccessTokenFromApi(): TwitchUser
    {
        $clientId = env('CLIENT_SECRET');
        $clientSecret = env('CLIENT_ID');

        $postFields = "client_id=$clientId&client_secret=$clientSecret&grant_type=client_credentials";
        $curl = curl_init('https://id.twitch.tv/oauth2/token');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        $dataRaw = json_decode($response, true);

        if (isset($dataRaw['access_token'])) {
            return new TwitchUser(1, $dataRaw['access_token'], $dataRaw['expires_in']);
        }

        throw new TwitchApiException("Internal server error.");
    }
}
