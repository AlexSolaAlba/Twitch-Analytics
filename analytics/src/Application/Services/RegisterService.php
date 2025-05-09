<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;

class RegisterService
{
    public function __construct()
    {
    }


    public function register($email): array
    {
        try {
            $key = bin2hex(random_bytes(16));
        } catch (RandomException) {
            return [
                'error' => 'Internal server error'
            ];
        }

        if ($this->guardarEnBBDD($email, $key)) {
            return [
                'api_key' => $key
            ];
        }

        return [
            'error' => 'Internal server error'
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function guardarEnBBDD($email, $key): bool
    {
        #$conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        $conexion = mysqli_connect(
            env('DB_HOST'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE')
        );

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
}
