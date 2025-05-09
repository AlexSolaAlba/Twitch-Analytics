<?php

header("Content-Type: application/json");

$key = bin2hex(random_bytes(16));
if (guardarEnBBDD($email, $key)) {
    $message = [
        'api_key' => $key
    ];
    echo json_encode($message);
} else {
    http_response_code(500);
    $error_message = [
        'error' => 'Internal server error'
    ];
    echo json_encode($error_message);
}


/**
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
function guardarEnBBDD($email, $key)
{
    $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
    #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");

    if (!$conexion) {
        return false;
    }

    $consulta = $conexion->prepare("SELECT userID FROM user WHERE userEmail = ?");
    $consulta->bind_param("s", $email);
    if (!$consulta->execute()) {
        return false;
    }
    $resultado = $consulta->get_result();
    $datos = $resultado->fetch_assoc();
    $consulta->close();

    if (isset($datos['userID'])) {
        $userId = $datos['userID'];
        $stmt = $conexion->prepare("UPDATE user SET userApiKey = ? WHERE userID = ?");
        $stmt->bind_param("si", $key, $userId);
    } else {
        $stmt = $conexion->prepare("INSERT INTO user (userEmail, userApiKey) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $key);
    }

    if (!$stmt->execute()) {
        return false;
    }

    $stmt->close();
    $conexion->close();
    return true;
}
