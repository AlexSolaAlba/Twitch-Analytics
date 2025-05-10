<?php

namespace TwitchAnalytics\Controllers\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Domain\DB\DataBaseHandler;

use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\ValidationException;
class TokenController extends BaseController
{
    private DataBaseHandler $databaseHandler;
    public function __construct(DataBaseHandler $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
    }

    public function __invoke(Request $request): JsonResponse{
        try {
            header("Content-Type: application/json");
            $metodo = $_SERVER['REQUEST_METHOD'];
            if (strcmp($metodo, 'POST') === 0) {
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                if (isset($_POST['email'])) {
                    $email = $_POST['email'];
                } elseif (isset($data['email'])) {
                    $email = $data['email'];
                }

                if (isset($_POST['api_key'])) {
                    $key = $_POST['api_key'];
                } elseif (isset($data['api_key'])) {
                    $key = $data['api_key'];
                }

                if (isset($email)) {
                    if (isset($key)) {
                        if ($this->comprobarEmail($email)) {
                            if ($this->comprobarApiKey($email, $key)) {
                                $token = bin2hex(random_bytes(16));
                                if ($this->guardarEnBBDD($email, $key, $token)) {
                                    return response()->json(['token' => $token]);
                                } else {
                                    return response()->json(['error' => 'Internal server error'], 500);
                                }
                            } else {
                                return response()->json(['error' => 'Unauthorized. API access token is invalid.'], 401);
                            }
                        } else {
                            return response()->json(['error' => 'The email must be a valid email address'], 400);
                        }
                    } else {
                        return response()->json(['error' => 'The api_key is mandatory'], 400);
                    }
                } else {
                    http_response_code(400);
                    return response()->json(['error' => 'The email is mandatory'], 400);
                }
            } else {
                return response()->json(['error' => 'Internal server error'], 500);
            }
        }catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    function comprobarEmail($email): bool
    {
        if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $conexion = $this->databaseHandler->connectWithDB();

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
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    function comprobarApiKey($email, $key): bool
    {
        $conexion = $this->databaseHandler->connectWithDB();

        if (!$conexion) {
            return false;
        }

        $consulta = $conexion->prepare("SELECT userApiKey FROM user WHERE userEmail = ?");
        $consulta->bind_param("s", $email);
        if (!$consulta->execute()) {
            return false;
        }
        $resultado = $consulta->get_result();
        $datos = $resultado->fetch_assoc();
        if ($key == $datos['userApiKey']) {
            return true;
        } else {
            return false;
        }
        $consulta->close();
    }

    function guardarEnBBDD($email, $key, $token): bool
    {
        $conexion = $this->databaseHandler->connectWithDB();

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

        $userId = $datos['userID'];
        $expiration = time() + 259200;
        $stmt = $conexion->prepare("UPDATE user SET userToken = ? , userTokenExpire = ?  WHERE userID = ?");
        $stmt->bind_param("sdi", $token, $expiration, $userId);


        if (!$stmt->execute()) {
            return false;
        }

        $stmt->close();
        $conexion->close();
        return true;
    }
}
