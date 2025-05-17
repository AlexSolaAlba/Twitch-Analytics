<?php

namespace TwitchAnalytics\Infraestructure\DB;

use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Models\User;
use TwitchAnalytics\Infraestructure\Exceptions\DBException;

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

    public function verifyToken(string $token): User
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        $stmt = $this->getUserWithToken($connection, $token);
        $this->checkStmtExecution($stmt);
        $dataRaw = $stmt->get_result();
        $user = $dataRaw->fetch_assoc();
        $stmt->close();
        $connection->close();
        if (($dataRaw->num_rows === 0) or ($user['userTokenExpire'] < time())) {
            throw new ApiKeyException('Unauthorized. Token is invalid or expired.');
        }

        return new User($user['userID'], $user['userEmail'], $user['userApiKey'], $user['userToken'], $user['userTokenExpire']);
    }

    private function getUserWithToken(false|\mysqli $connection, string $token): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("SELECT * FROM user WHERE userToken = ?");
        $stmt->bind_param("s", $token);
        return $stmt;
    }

    public function getTwitchUserFromDB(): TwitchUser
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->getTwitchUserQuery($connection);
            $this->checkStmtExecution($stmt);

            $dataRaw = $stmt->get_result();
            $twitchUser = $dataRaw->fetch_assoc();
            $accessToken = $twitchUser['accessToken'];
            $tokenExpire = $twitchUser['tokenExpire'];
            $stmt->close();
            return new TwitchUser(1, $accessToken, $tokenExpire);
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function getTwitchUserQuery(false|\mysqli $connection): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("SELECT accessToken, tokenExpire FROM token WHERE tokenID =?");
        $tokenId = 1;
        $stmt->bind_param("i", $tokenId);
        return $stmt;
    }

    public function updateTokenInDB($accessToken, $expiresIn): void
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $expiresAt = time() + $expiresIn;
            $stmt = $this->updateTokenQuery($connection, $accessToken, $expiresAt);
            $this->checkStmtExecution($stmt);

            $stmt->close();
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function updateTokenQuery(false|\mysqli $connection, string $accessToken, int $expiresAt): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("UPDATE token set accessToken = ?, tokenExpire = ? WHERE tokenID = 1");
        $stmt->bind_param("si", $accessToken, $expiresAt);
        return $stmt;
    }

    public function insertStreamerIntoDB($streamer): void
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->insertStreamerInDBQuery($connection, $streamer);
            $this->checkStmtExecution($stmt);

            $stmt->close();
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    public function insertStreamerInDBQuery($connection, Streamer $streamer): false|\mysqli_stmt
    {
        $streamerId = $streamer->getStreamerId();
        $login = $streamer->getLogin();
        $displayName = $streamer->getDisplayName();
        $type = $streamer->getType();
        $broadcasterType = $streamer->getBroadcasterType();
        $description = $streamer->getDescription();
        $profileImageUrl = $streamer->getProfileImageUrl();
        $offlineImageUrl = $streamer->getOfflineImageUrl();
        $viewCount = $streamer->getViewCount();
        $createdAt = $streamer->getCreatedAt();

        $stmt = $connection->prepare(
            "INSERT INTO userstwitch(id, user_login, display_name, user_type, broadcaster_type,
        user_description, profile_image_url, offline_image_url, view_count, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssssssss",
            $streamerId,
            $login,
            $displayName,
            $type,
            $broadcasterType,
            $description,
            $profileImageUrl,
            $offlineImageUrl,
            $viewCount,
            $createdAt
        );
        return $stmt;
    }

    public function getStreamerFromDB(int $streamerId): bool|Streamer
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->getStreamerQuery($connection, $streamerId);
            $this->checkStmtExecution($stmt);
            $streamerRaw = $stmt->get_result();
            $stmt->close();
            if ($streamerRaw->num_rows > 0) {
                $streamer = $streamerRaw->fetch_assoc();
                return new Streamer(
                    $streamer["id"],
                    $streamer["user_login"],
                    $streamer["display_name"],
                    $streamer["user_type"],
                    $streamer["broadcaster_type"],
                    $streamer["user_description"],
                    $streamer["profile_image_url"],
                    $streamer["offline_image_url"],
                    $streamer["view_count"],
                    $streamer["created_at"]
                );
            }
            return false;
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function getStreamerQuery(false|\mysqli $connection, $userId): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("SELECT * FROM usersTwitch where id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt;
    }

    public function deleteTestStreamerFromDB(): void
    {
        $connection = $this->connectWithDB();
        $this->checkConnection($connection);

        try {
            $stmt = $this->deleteTestStreamerFromDBQuery($connection);
            $this->checkStmtExecution($stmt);
            $stmt->close();
        } finally {
            if ($connection instanceof \mysqli) {
                $connection->close();
            }
        }
    }

    private function deleteTestStreamerFromDBQuery(false|\mysqli $connection): false|\mysqli_stmt
    {
        $stmt = $connection->prepare("DELETE FROM usersTwitch where id = ?");
        $streamerId = 4;
        $stmt->bind_param("i", $streamerId);
        return $stmt;
    }
}
