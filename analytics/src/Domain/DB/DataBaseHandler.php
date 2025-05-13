<?php

namespace TwitchAnalytics\Domain\DB;

use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Models\User;

class DataBaseHandler
{
    public function connectWithDB(): false|\mysqli
    {
        return mysqli_connect(
            env('DB_HOST'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE')
        );
    }

    public function checkConnection(false|\mysqli $connection): void
    {
        if (!$connection) {
            throw new DBException('Internal server error.');
        }
    }

    public function checkStmtExecution(false|\mysqli_stmt $stmt): void
    {
        if (!$stmt->execute()) {
            throw new DBException('Internal server error.');
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function saveUserAndApiKeyInDB($email, $key): User
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);


        $stmt = $this->getUserIDWithEmailFromDB($connection, $email);

        $this->checkStmtExecution($stmt);

        $userIdRaw = $stmt->get_result();
        $userId = $userIdRaw->fetch_assoc();
        $stmt->close();

        if ($this->existUserID($userId)) {
            $userId = $userId['userID'];
            $stmt = $this->updateApiKey($connection, $key, $userId);
        } else {
            $stmt = $this->insertNewUserAndApiKey($connection, $email, $key);
        }

        $this->checkStmtExecution($stmt);

        $stmt->close();
        $connection->close();

        return new User($email, $key, "", 0);
    }

    /**
     * @param false|\mysqli $connection
     * @param $email
     * @return false|\mysqli_stmt
     */
    public function getUserIDWithEmailFromDB(false|\mysqli $connection, $email): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("SELECT userID FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        return $stmt;
    }


    public function existUserID(false|array|null $userId): bool
    {
        return isset($userId['userID']);
    }

    /**
     * @param false|\mysqli $connection
     * @param $key
     * @param mixed $userId
     * @return false|\mysqli_stmt
     */
    public function updateApiKey(false|\mysqli $connection, $key, mixed $userId): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("UPDATE user SET userApiKey = ? WHERE userID = ?");
        $stmt->bind_param("si", $key, $userId);
        return $stmt;
    }

    /**
     * @param false|\mysqli $connection
     * @param $email
     * @param $key
     * @return false|\mysqli_stmt
     */
    public function insertNewUserAndApiKey(false|\mysqli $connection, $email, $key): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("INSERT INTO user (userEmail, userApiKey) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $key);
        return $stmt;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function checkEmailExists($email): void
    {
        $connection = $this->connectWithDB();

        $this->checkConnection($connection);

        $stmt = $this->getUserIDWithEmailFromDB($connection, $email);
        $this->checkStmtExecution($stmt);

        $dataRaw = $stmt->get_result();
        $data = $dataRaw->fetch_assoc();
        $stmt->close();
        if (!isset($data['userID'])) {
            throw new DBException('The email must be a valid email address');
        }
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function checkApiKeyExists($email, $key): void
    {
        $connection = $this->connectWithDB();

        $this->checkConnection($connection);

        $stmt = $connection->prepare("SELECT userApiKey FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        $this->checkStmtExecution($stmt);

        $dataRaw = $stmt->get_result();
        $data = $dataRaw->fetch_assoc();
        $stmt->close();
        if ($key != $data['userApiKey']) {
            throw new ApiKeyException('Unauthorized. API access token is invalid.');
        }
    }

    public function insertTokenIntoDB($email, $token): void
    {
        $connection = $this->connectWithDB();

        $this->checkConnection($connection);

        $stmt = $connection->prepare("SELECT userID FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);

        $this->checkStmtExecution($stmt);
        $dataRaw = $stmt->get_result();
        $data = $dataRaw->fetch_assoc();
        $stmt->close();

        $userId = $data['userID'];
        $expiration = time() + 259200;
        $stmt = $connection->prepare("UPDATE user SET userToken = ? , userTokenExpire = ?  WHERE userID = ?");
        $stmt->bind_param("sdi", $token, $expiration, $userId);


        $this->checkStmtExecution($stmt);

        $stmt->close();
        $connection->close();
    }
}
