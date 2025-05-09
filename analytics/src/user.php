<?php

include("restaurarToken.php");
verificarTokenUser();
header("Content-Type: application/json");
$requestMethod = $_SERVER['REQUEST_METHOD'];
$headerData = getValidToken();
$clientID = $headerData['clientId'];
$accessToken = $headerData['accessToken'];
if (strcmp($requestMethod, 'GET') === 0 && isset($_GET['id']) && count($_GET) === 1) {
    $userId = $_GET['id'];
    if (preg_match("/[0-9]/", $userId) === 1) {
        returnUserInfo($userId, $clientID, $accessToken);
    } else {
        http_response_code(400);
        $error_message = [
            'error' => 'Invalid or missing id parameter.'
        ];
        echo json_encode($error_message);
    }
} else {
    http_response_code(400);
    $error_message = [
        'error' => 'Invalid or missing id parameter.'
    ];
    echo json_encode($error_message);
}


/**
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
function returnUserInfo($userId, $clientID, $accessToken)
{
    $conection = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conection = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conection) {
        http_response_code(500);
        $errorMessage = [
            'error' => 'Internal server error.'
        ];
        echo json_encode($errorMessage);
        exit();
    }
    $user = $conection->query("SELECT * FROM usersTwitch where id = $userId");
    if ($user == false) {
        http_response_code(400);
        die("Error en la consulta: " . mysqli_error($conection));
    }

    if ($user->num_rows > 0) {
        while ($row = $user->fetch_assoc()) {
            $data = [
                "id" => $row["id"],
                "login" => $row["user_login"],
                "display_name" => $row["display_name"],
                "type" => $row["user_type"],
                "broadcaster_type" => $row["broadcaster_type"],
                "description" => $row["user_description"],
                "profile_image_url" => $row["profile_image_url"],
                "offline_image_url" => $row["offline_image_url"],
                "view_count" => $row["view_count"],
                "created_at" => $row["created_at"]
            ];
        }
        echo json_encode($data);
    } else {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$userId");
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
                    insertIntoDB($responseData['data'][0]);
                    echo $response;
                } else {
                    http_response_code(404);
                    $errorMessage = [
                        'error' => 'User not found.'
                    ];
                    echo json_encode($errorMessage);
                }
                break;
            case 400:
                http_response_code(400);
                $errorMessage = [
                    'error' => 'Invalid or missing id parameter.'
                ];
                echo json_encode($errorMessage);
                break;
            case 401:
                http_response_code(401);
                $errorMessage = [
                    'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
                ];
                echo json_encode($errorMessage);
                break;
            case 404:
                http_response_code(404);
                $errorMessage = [
                    'error' => 'User not found.'
                ];
                echo json_encode($errorMessage);
                break;
            case 500:
                http_response_code(500);
                $errorMessage = [
                    'error' => 'Internal server error.'
                ];
                echo json_encode($errorMessage);
                break;
        }
    }
    $conection->close();
}

function insertIntoDB($user)
{
    $conection = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conection = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conection) {
        http_response_code(500);
        $errorMessage = [
            'error' => 'Internal server error. Please try again later.'
        ];
        echo json_encode($errorMessage);
        exit();
    }

    $query = $conection->prepare(
        "INSERT INTO usersTwitch(id, user_login, display_name, user_type, broadcaster_type,
        user_description, profile_image_url, offline_image_url, view_count, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)"
    );
    $query->bind_param(
        "ssssssssss",
        $user["id"],
        $user["login"],
        $user["display_name"],
        $user["type"],
        $user["broadcaster_type"],
        $user["description"],
        $user["profile_image_url"],
        $user["offline_image_url"],
        $user["view_count"],
        $user["created_at"]
    );
    if (!$query->execute()) {
        $query->close();
        $conection->close();
        http_response_code(400);
        die("Error en la consulta: " . mysqli_error($conection));
    }
    $query->close();
    $conection->close();
}
