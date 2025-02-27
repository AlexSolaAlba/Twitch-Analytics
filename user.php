<?php

include("restaurarToken.php");
$usuario = verificarTokenUser();
header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];
$headerData = getValidToken();
$clientID = $headerData['clientId'];
$accesstoken = $headerData['accessToken'];
if (strcmp($metodo, 'GET') === 0 && isset($_GET['id']) && count($_GET) === 1) {
    $id = $_GET['id'];
    if (preg_match("/[0-9]/", $id) === 1) {
        leerCache($id, $clientID, $accesstoken);
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

function guardarEnBBDD($user)
{
    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conexion) {
        http_response_code(500);
        $error_message = [
            'error' => 'Internal server error. Please try again later.'
        ];
        echo json_encode($error_message);
        exit();
    }

    $consulta = $conexion->prepare(
        "INSERT INTO usersTwitch(id, user_login, display_name, user_type, broadcaster_type,
        user_description, profile_image_url, offline_image_url, view_count, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)"
    );
    $consulta->bind_param(
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
    if (!$consulta->execute()) {
        $consulta->close();
        $conexion->close();
        http_response_code(400);
        die("Error en la consulta: " . mysqli_error($conexion));
    }
    $consulta->close();
    $conexion->close();
}
/**
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
function leerCache($userId, $clientID, $accesstoken)
{
    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conexion) {
        http_response_code(500);
        $error_message = [
            'error' => 'Internal server error. Please try again later.'
        ];
        echo json_encode($error_message);
        exit();
    }
    $resultado = $conexion->query("SELECT * FROM usersTwitch where id = $userId");
    if ($resultado == false) {
        http_response_code(400);
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $datos = [
                "id" => $fila["id"],
                "login" => $fila["user_login"],
                "display_name" => $fila["display_name"],
                "type" => $fila["user_type"],
                "broadcaster_type" => $fila["broadcaster_type"],
                "description" => $fila["user_description"],
                "profile_image_url" => $fila["profile_image_url"],
                "offline_image_url" => $fila["offline_image_url"],
                "view_count" => $fila["view_count"],
                "created_at" => $fila["created_at"]
            ];
        }
        echo json_encode($datos);
    } else {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$userId");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Client-ID: $clientID",
            "Authorization: Bearer $accesstoken"
        ]);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response_data = json_decode($response, true);


        switch ($http_code) {
            case 200:
                if (isset($response_data['data'][0])) {
                    guardarEnBBDD($response_data['data'][0]);
                    echo $response;
                } else {
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
    }
    $conexion->close();
}
