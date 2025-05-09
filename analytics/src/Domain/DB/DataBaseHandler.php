<?php

namespace TwitchAnalytics\Domain\DB;

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
    public function saveUserAndApiKeyInDB($email, $key): void
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

    /**
     * @param false|array|null $datos
     * @return bool
     */
    public function existUserID(false|array|null $datos): bool
    {
        return isset($datos['userID']);
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
}
