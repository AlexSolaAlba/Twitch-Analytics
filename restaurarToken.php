<?php

function getAccessToken()
{
    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conexion) {
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $consulta = $conexion->prepare("SELECT clientId, clientSecret FROM token WHERE tokenID = 1");
    if (!$consulta->execute()) {
        $consulta->close();
        $conexion->close();
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }

    $resultado = $consulta->get_result();
    $datos = $resultado->fetch_assoc();
    $clientId = $datos['clientId'];
    $clientSecret = $datos['clientSecret'];
    $consulta->close();
    $conexion->close();

    $postFields = "client_id=$clientId&client_secret=$clientSecret&grant_type=client_credentials";
    $ch = curl_init('https://id.twitch.tv/oauth2/token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        saveTokenToDB($data['access_token'], $data['expires_in']);
        return $data['access_token'];
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
}

function saveTokenToDB($accessToken, $expiresIn)
{
    $expiresAt = time() + $expiresIn;
    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conexion) {
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $consulta = $conexion->prepare("UPDATE token set accessToken = ?, tokenExpire = ? WHERE tokenID = 1");
    $consulta->bind_param("si", $accessToken, $expiresAt);
    if (!$consulta->execute()) {
        $consulta->close();
        $conexion->close();
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $consulta->close();
    $conexion->close();
}

function getTokenFromDB()
{
    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conexion) {
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $consulta = $conexion->prepare("SELECT accessToken, tokenExpire, clientId FROM token WHERE tokenID =?");
    $id = 1;
    $consulta->bind_param("i", $id);
    if (!$consulta->execute()) {
        $consulta->close();
        $conexion->close();
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $resultado = $consulta->get_result();
    $datos = $resultado->fetch_assoc();
    $accessToken = $datos['accessToken'];
    $tokenExpire = $datos['tokenExpire'];
    $clientId = $datos['clientId'];
    $consulta->close();
    $conexion->close();
    return ['accessToken' => $accessToken, 'tokenExpire' => $tokenExpire, 'clientId' => $clientId];
}

function getValidToken()
{
    $tokenData = getTokenFromDB();
    if (!$tokenData || time() >= $tokenData['tokenExpire']) {
        $newToken = getAccessToken();
        return ['accessToken' => $newToken, 'clientId' => $tokenData['clientId']];
    }
    return ['accessToken' => $tokenData['accessToken'], 'clientId' => $tokenData['clientId']];
}

function verificarTokenUser()
{
    $headers = getallheaders();
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $headers['Authorization'] ?? null;
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."]);
        exit();
    }

    list($type, $token) = explode(' ', $authHeader, 2);
    if ($type !== 'Bearer' || empty($token)) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."]);
        exit();
    }

    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    if (!$conexion) {
        $conexion . close();
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $stmt = $conexion->prepare("SELECT * FROM user WHERE userToken = ?");
    $stmt->bind_param("s", $token);
    if (!$stmt->execute()) {
        $stmt->close();
        $conexion->close();
        http_response_code(500);
        echo json_encode(["error" => "Internal server error."]);
        exit();
    }
    $result = $stmt->get_result();

    $usuario = $result->fetch_assoc();
    $stmt->close();
    $conexion->close();
    if (($result->num_rows === 0) or ($usuario['userTokenExpire'] < time())) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."]);
        exit();
    }
}
