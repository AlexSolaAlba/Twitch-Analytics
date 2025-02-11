<?php
    header("Content-Type: application/json");
	$metodo = $_SERVER['REQUEST_METHOD'];
	$accestoken = '07lhzqjw4ckfffzlcqad1rr3hzbvfk';
    $clientID = 'mqf0orb9t2ufb34nhd3em686qpb8xc';
	if(strcmp($metodo, 'GET') === 0 && isset($_GET['limit']) && count($_GET) === 1){
        $limit = $_GET['limit'];
      	if(preg_match("/[0-9]/",$limit) === 1){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/streams?first=$limit");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Client-ID: $clientID",
                "Authorization: Bearer $accestoken"
            ]);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $response_data = json_decode($response, true);

            $filtered_streams = [];

            if (isset($response_data['data'])) {
                foreach ($response_data['data'] as $stream) {
                    $id =  $stream['user_id'];
                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$id");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Client-ID: $clientID",
                        "Authorization: Bearer $accestoken"
                    ]);
                    $response2 = curl_exec($ch);
                    curl_close($ch);
                    $response_data2 = json_decode($response2, true);

                    $filtered_streams[] = [
                        'stream_id' => $stream['id'],
                        'user_id' => $stream['user_id'],
                        'user_name' => $stream['user_name'],
                        'viewer_count' => $stream['viewer_count'],
                        'user_display_name' => $stream['user_name'],
                        'title' => $stream['title'],
                        'profile_image_url' =>  $response_data2['data'][0]['profile_image_url']
                    ];
                }
            }

            switch($http_code){
                case 200:
                    echo json_encode($filtered_streams,JSON_UNESCAPED_UNICODE);
                    break;
                case 400:
                	http_response_code(400);
                    $error_message = [
                        'error' => 'Invalid or missing limit parameter.'
                    ];
                    echo json_encode($error_message);
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
        }else{
          	http_response_code(400);
            $error_message = [
                'error' => 'Invalid or missing limit parameter.'
            ];
            echo json_encode($error_message);
        }
    }else{
      	http_response_code(400);
        $error_message = [
            'error' => 'Invalid or missing limit parameter.'
        ];
        echo json_encode($error_message);
    }
?>