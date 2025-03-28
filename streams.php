<?php

include("restaurarToken.php");
@SuppressWarnings(PHPMD . ElseExpression);
@SuppressWarnings(PHPMD . CyclomaticComplexity);
$usuario = verificarTokenUser();
header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];
$headerData = getValidToken();
$clientID = $headerData['clientId'];
$accesstoken = $headerData['accessToken'];

if (strcmp($metodo, 'GET') === 0) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/streams?first=100");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $clientID",
        "Authorization: Bearer $accesstoken"
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response_data = json_decode($response, true);

    $filtered_streams = [];

    if (isset($response_data['data'])) {
        foreach ($response_data['data'] as $stream) {
            $filtered_streams[] = [
                'title' => $stream['title'],
                'user_name' => $stream['user_name']
            ];
        }
    }

    switch ($http_code) {
        case 200:
            echo json_encode($filtered_streams, JSON_UNESCAPED_UNICODE);
            break;
        case 401:
            http_response_code(401);
            $error_message = [
                'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
            ];
            echo json_encode($error_message);
            break;
        case 500:
            http_response_code(500);
            $error_message = [
                'error' => 'Internal server error.'
            ];
            echo json_encode($error_message);
            break;
    }
} else {
    http_response_code(500);
    $error_message = [
        'error' => 'Internal server error.'
    ];
    echo json_encode($error_message);
}
