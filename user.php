<?php
    header("Content-Type: application/json");
	$metodo = $_SERVER['REQUEST_METHOD'];
	$accestoken = '07lhzqjw4ckfffzlcqad1rr3hzbvfk';
    $clientID = 'mqf0orb9t2ufb34nhd3em686qpb8xc';
	if(strcmp($metodo, 'GET') === 0 && isset($_GET['id']) && count($_GET) === 1){
        $id = $_GET['id'];
      	if(preg_match("/[0-9]/",$id) === 1){
          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$id");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HTTPHEADER, [
              "Client-ID: $clientID",
              "Authorization: Bearer $accestoken"
          ]);
          $response = curl_exec($ch);
          $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);
          $response_data = json_decode($response, true);
          switch($http_code){
              case 200:
                  if (isset($response_data['data'][0])) {
                      echo $response;
                  }else{
                      http_response_code(404);
                      $error_message = [
                          'error' => 'User not found.',
                          'status_code' => 404,
                          'response' => json_decode($response, true)
                      ];
                      echo json_encode($error_message);
                  }
                  break;
              case 400:
              	  http_response_code(400);
                  $error_message = [
                      'error' => 'Invalid or missing id parameter.',
                      'status_code' => $http_code,
                      'response' => json_decode($response, true)
                  ];
                  echo json_encode($error_message);
                  break;
              case 401:
              	  http_response_code(401);
                  $error_message = [
                      'error' => 'Unauthorized. Twitch access token is invalid or has expired.',
                      'status_code' => $http_code,
                      'response' => json_decode($response, true)
                  ];
                  echo json_encode($error_message);
                  break;
              case 404:
              	  http_response_code(404);
                  $error_message = [
                      'error' => 'User not found.',
                      'status_code' => $http_code,
                      'response' => json_decode($response, true)
                  ];
                  echo json_encode($error_message);
                  break;
              case 500:
              	  http_response_code(500);
                  $error_message = [
                      'error' => 'Internal server error.',
                      'status_code' => $http_code,
                      'response' => json_decode($response, true)
                  ];
                  echo json_encode($error_message);
                  break;
        	}
        }else{
            http_response_code(400);
          	$error_message = [
                'error' => 'Invalid or missing id parameter.',
                'status_code' => 400
            ];
            echo json_encode($error_message);
        }
    }else{
      	http_response_code(400);
        $error_message = [
            'error' => 'Invalid or missing id parameter.',
            'status_code' => 400
        ];
        echo json_encode($error_message);
    }
?>