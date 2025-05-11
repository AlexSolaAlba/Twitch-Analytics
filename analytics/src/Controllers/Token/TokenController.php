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

            if ($this->checkEmailExists($email)) {
                if ($this->checkApiKeyExists($email, $key)) {
                    $token = $this->keyGenerator->generateRandomKey();
                    if ($this->insertTokenIntoDB($email, $key, $token)) {
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
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function checkEmailExists($email): bool
    {
        $connection = $this->databaseHandler->connectWithDB();

        if (!$connection) {
            return false;
        }

        $stmt = $connection->prepare("SELECT userID FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            return false;
        }
        $dataRaw = $stmt->get_result();
        $data = $dataRaw->fetch_assoc();
        $stmt->close();
        if (isset($data['userID'])) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function checkApiKeyExists($email, $key): bool
    {
        $connection = $this->databaseHandler->connectWithDB();

        if (!$connection) {
            return false;
        }

        $stmt = $connection->prepare("SELECT userApiKey FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            return false;
        }
        $dataRaw = $stmt->get_result();
        $data = $dataRaw->fetch_assoc();
        if ($key == $data['userApiKey']) {
            return true;
        } else {
            return false;
        }
        $stmt->close();
    }

    public function insertTokenIntoDB($email, $key, $token): bool
    {
        $connection = $this->databaseHandler->connectWithDB();

        if (!$connection) {
            return false;
        }

        $stmt = $connection->prepare("SELECT userID FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            return false;
        }
        $dataRaw = $stmt->get_result();
        $data = $dataRaw->fetch_assoc();
        $stmt->close();

        $userId = $data['userID'];
        $expiration = time() + 259200;
        $stmt = $connection->prepare("UPDATE user SET userToken = ? , userTokenExpire = ?  WHERE userID = ?");
        $stmt->bind_param("sdi", $token, $expiration, $userId);


        if (!$stmt->execute()) {
            return false;
        }

        $stmt->close();
        $connection->close();
        return true;
    }
}
