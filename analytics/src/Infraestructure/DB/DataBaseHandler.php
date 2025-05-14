<?php

namespace TwitchAnalytics\Infraestructure\DB;

use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Models\User;

class DataBaseHandler
{
    private function connectWithDB(): false|\mysqli
    {
        return mysqli_connect(
            env('DB_HOST'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE')
        );
    }

    private function checkConnection(false|\mysqli $connection): void
    {
        if (!$connection) {
            throw new DBException('Internal server error.');
        }
    }

    private function checkStmtExecution(false|\mysqli_stmt $stmt): void
    {
        if (!$stmt->execute()) {
            throw new DBException('Internal server error.');
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function saveUserAndApiKeyInDB(string $email, string $key): User
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        $stmt = null;

        try {
            $stmt = $this->getUserIDWithEmailFromDB($connection, $email);
            $this->checkStmtExecution($stmt);

            $userIdRaw = $stmt->get_result();
            $userIdAssoc = $userIdRaw->fetch_assoc();
            $stmt->close();

            if ($this->existUserID($userIdAssoc)) {
                $userId = $userIdAssoc['userID'];
                $stmt = $this->updateApiKey($connection, $key, $userId);
                $this->checkStmtExecution($stmt);
            } else {
                $stmt = $this->insertNewUserAndApiKey($connection, $email, $key);
                $this->checkStmtExecution($stmt);
                $stmt->close();

                $stmt = $this->getUserIDWithEmailFromDB($connection, $email);
                $this->checkStmtExecution($stmt);

                $userIdRaw = $stmt->get_result();
                $userIdAssoc = $userIdRaw->fetch_assoc();
                $userId = $userIdAssoc['userID'];
            }
            $stmt->close();

            return new User($userId, $email, $key, "", 0);
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function getUserIDWithEmailFromDB(false|\mysqli $connection, string $email): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("SELECT userID FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        return $stmt;
    }

    private function existUserID(false|array|null $userId): bool
    {
        return isset($userId['userID']);
    }

    private function updateApiKey(false|\mysqli $connection, string $key, int $userId): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("UPDATE user SET userApiKey = ? WHERE userID = ?");
        $stmt->bind_param("si", $key, $userId);
        return $stmt;
    }

    private function insertNewUserAndApiKey(false|\mysqli $connection, string $email, string $key): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("INSERT INTO user (userEmail, userApiKey) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $key);
        return $stmt;
    }

    public function checkUserExistsInDB(string $email, string $key): User
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        $stmt = null;

        try {
            $this->checkEmailExists($connection, $email);
            $this->checkApiKeyExists($connection, $email, $key);

            $stmt = $this->getUserIDWithEmailFromDB($connection, $email);
            $this->checkStmtExecution($stmt);

            $userIdRaw = $stmt->get_result();
            $userIdAssoc = $userIdRaw->fetch_assoc();
            $userId = $userIdAssoc['userID'];
            $stmt->close();

            return new User($userId, $email, $key, "", 0);
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function checkEmailExists(\mysqli $connection, string $email): void
    {
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
    private function checkApiKeyExists(\mysqli $connection, string $email, string $key): void
    {
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

    public function insertTokenIntoDB(User $user, string $token): void
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        $stmt = null;

        try {
            $expiration = time() + 259200;
            $stmt = $this->updateTokenAndTokenExpire($connection, $token, $expiration, $user->getUserId());
            $this->checkStmtExecution($stmt);

            $user->setToken($token);
            $user->setTokenExpire($expiration);
        } finally {
            if ($stmt instanceof \mysqli_stmt) {
                $stmt->close();
            }
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function updateTokenAndTokenExpire(false|\mysqli $connection, string $token, int $expiration, int $userId): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("UPDATE user SET userToken = ? , userTokenExpire = ?  WHERE userID = ?");
        $stmt->bind_param("sdi", $token, $expiration, $userId);
        return $stmt;
    }
}
