<?php

namespace TwitchAnalytics\Controllers\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Domain\DB\DataBaseHandler;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

class TokenController extends BaseController
{
    private DataBaseHandler $databaseHandler;
    private TokenValidator $validator;
    private RandomKeyGenerator $keyGenerator;
    public function __construct(DataBaseHandler $databaseHandler, TokenValidator $validator, RandomKeyGenerator $keyGenerator)
    {
        $this->databaseHandler = $databaseHandler;
        $this->validator = $validator;
        $this->keyGenerator = $keyGenerator;
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validateEmail($request->get('email'));
            $key = $this->validator->validateKey($request->get('api_key'));

            if (isset($email)) {
                if (isset($key)) {
                    if ($this->comprobarEmail($email)) {
                        if ($this->comprobarApiKey($email, $key)) {
                            $token = $this->keyGenerator->generateRandomKey();
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
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function comprobarEmail($email): bool
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
    public function comprobarApiKey($email, $key): bool
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

    public function guardarEnBBDD($email, $key, $token): bool
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
