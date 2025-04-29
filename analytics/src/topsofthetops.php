<?php

header("Content-Type: application/json");
include("restaurarToken.php");
$usuario = verificarTokenUser();
$metodo = $_SERVER['REQUEST_METHOD'];
$headerData = getValidToken();
$clientID = $headerData['clientId'];
$accesstoken = $headerData['accessToken'];
if (strcmp($metodo, 'GET') === 0) {
    $filtered_videos = leerCache();
    $currentDate = time();
    $interval = $currentDate - strtotime($filtered_videos[0]["created_at"]);
    foreach ($filtered_videos as $game) {
        array_pop($game);
    }
    if ($interval > 600 || (isset($_GET["since"]) && count($_GET) === 1)) {
        $since = $_GET["since"];
        if (!isset($_GET["since"]) || ((preg_match("/[0-9]/", $since) === 1) && ($since < $interval))) {
            resetCache();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/games/top?first=3");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Client-ID: $clientID",
                "Authorization: Bearer $accesstoken"
            ]);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $response_data = json_decode($response, true);

            $filtered_videos = [];

            if (isset($response_data["data"])) {
                foreach ($response_data["data"] as $game) {
                    $id = $game["id"];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/videos?game_id=$id&first=40&sort=views");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Client-ID: $clientID",
                        "Authorization: Bearer $accesstoken"
                    ]);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response_data2 = json_decode($response, true);
                    $videoCount = 0;
                    $viewCount = 0;
                    foreach ($response_data2["data"] as $video) {
                        if ($video["user_name"] == $response_data2["data"][0]["user_name"]) {
                            $videoCount++;
                            $viewCount += $video["view_count"];
                        }
                    }
                    $total_videos = count($response_data2["data"]);
                    $total_views = count($response_data2["data"]);
                    $filtered_videos[] = [
                        "game_id" => $game["id"],
                        "game_name" => $game["name"],
                        "user_name" => $response_data2["data"][0]["user_name"],
                        "total_videos" => $videoCount,
                        "total_views" => $viewCount,
                        "most_viewed_title" => $response_data2["data"][0]["title"],
                        "most_viewed_views" => $response_data2["data"][0]["view_count"],
                        "most_viewed_duration" => $response_data2["data"][0]["duration"],
                        "most_viewed_created_at" => $response_data2["data"][0]["created_at"]
                    ];
                }
                guardarEnBBDD($filtered_videos);
            }
            switch ($http_code) {
                case 200:
                    echo json_encode($filtered_videos, JSON_UNESCAPED_UNICODE);
                    break;
                case 400:
                    http_response_code(400);
                    $error_message = [
                        'error' => 'Bad request. Invalid or missing parameters.'
                    ];
                    echo json_encode($error_message);
                    break;
                case 401:
                    http_response_code(401);
                    $error_message = [
                        'error' => 'Unauthorized. Token is invalid or has expired.'
                    ];
                    echo json_encode($error_message);
                    break;
                case 404:
                    http_response_code(404);
                    $error_message = [
                        'error' => 'Not found. No data available.'
                    ];
                    echo json_encode($error_message);
                    break;
                case 500:
                    http_response_code(500);
                    $error_message = [
                        'error' => 'Internal server error. Please try again later.'
                    ];
                    echo json_encode($error_message);
                    break;
            }
        } else {
            if (!preg_match("/[0-9]/", $since) !== 1) {
                http_response_code(400);
                $error_message = [
                    'error' => 'Bad Request. Invalid or missing parameters.'
                ];
                echo json_encode($error_message);
            }
        }
    } else {
        echo json_encode($filtered_videos, JSON_UNESCAPED_UNICODE);
    }
}

function resetCache()
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
    if (!$conexion->query("DELETE FROM topscache")) {
        $conexion->close();
        http_response_code(400);
        die("Error en la consulta: " . mysqli_error($conexion));
    }
    $conexion->close();
}

function guardarEnBBDD($videos)
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
        "INSERT INTO topscache(game_id, game_name, user_name, total_videos, total_views,
            most_viewed_title, most_viewed_views, most_viewed_duration, most_viewed_created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    foreach ($videos as $video) {
        $consulta->bind_param(
            "isiiisiss",
            $video["game_id"],
            $video["game_name"],
            $video["user_name"],
            $video["total_videos"],
            $video["total_views"],
            $video["most_viewed_title"],
            $video["most_viewed_views"],
            $video["most_viewed_duration"],
            $video["most_viewed_created_at"]
        );
        if (!$consulta->execute()) {
            $consulta->close();
            $conexion->close();
            http_response_code(400);
            die("Error en la consulta: " . mysqli_error($conexion));
        }
    }
    $consulta->close();
    $conexion->close();
}
/**
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
function leerCache()
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
    $resultado = $conexion->query("SELECT * FROM topscache");
    if ($resultado == false) {
        http_response_code(400);
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = [
                "game_id" => $fila["game_id"],
                "game_name" => $fila["game_name"],
                "user_name" => $fila["user_name"],
                "total_videos" => $fila["total_videos"],
                "total_views" => $fila["total_views"],
                "most_viewed_title" => $fila["most_viewed_title"],
                "most_viewed_views" => $fila["most_viewed_views"],
                "most_viewed_duration" => $fila["most_viewed_duration"],
                "most_viewed_created_at" => $fila["most_viewed_created_at"],
                "created_at" => $fila["created_at"]
            ];
        }
    } else {
        http_response_code(404);
        $datos = [];
        echo "error: Not Found. No data available.";
    }
    $conexion->close();
    return $datos;
}
