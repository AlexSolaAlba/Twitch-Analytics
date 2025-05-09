<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Exceptions\ApplicationException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

class RegisterService
{
    private RandomKeyGenerator $keyGenerator;
    public function __construct(RandomKeyGenerator $keyGenerator)
    {
        $this->keyGenerator = $keyGenerator;
    }


    /**
     * @throws RandomException
     */
    public function register($email): array
    {
        try {
            $key = $this->keyGenerator->generateRandomKey();
        } catch (RandomException) {
            throw new RandomException('Internal server error');
        }

        if ($this->guardarEnBBDD($email, $key)) {
            return [
                'api_key' => $key
            ];
        }

        throw new ApplicationException('Internal server error');
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
