<?php
    include("restaurarToken.php");
    $usuario = verificarTokenUser(); 
    header("Content-Type: application/json");
	$metodo = $_SERVER['REQUEST_METHOD'];
	$headerData = getValidToken();
    $clientID = $headerData['clientId'];
    $accesstoken = $headerData['accessToken'];
	if(strcmp($metodo, 'GET') === 0 && isset($_GET['id']) && count($_GET) === 1){
        $id = $_GET['id'];
      	if(preg_match("/[0-9]/",$id) === 1){
          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$id");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HTTPHEADER, [
              "Client-ID: $clientID",
              "Authorization: Bearer $accesstoken"
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
                          'error' => 'User not found.'
                      ];
                      echo json_encode($error_message);
                  }
                  break;
              case 400:
              	  http_response_code(400);
                  $error_message = [
                      'error' => 'Invalid or missing id parameter.'
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
              case 404:
              	  http_response_code(404);
                  $error_message = [
                      'error' => 'User not found.'
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
                'error' => 'Invalid or missing id parameter.'
            ];
            echo json_encode($error_message);
        }
    }else{
      	http_response_code(400);
        $error_message = [
            'error' => 'Invalid or missing id parameter.'
        ];
        echo json_encode($error_message);
    }
?>