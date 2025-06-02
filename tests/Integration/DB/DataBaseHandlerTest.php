<?php

namespace Integration\DB;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Models\Streamer;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Models\User;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Exceptions\DBException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DataBaseHandlerTest extends TestCase
{
    private DataBaseHandler $dataBaseHandler;
    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DataBaseHandler();
    }

    private function deleteUserByEmail(string $email): void
    {
        $mysqli = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
        $stmt = $mysqli->prepare("DELETE FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }

    private function selectUserByEmail(string $email): User
    {
        $mysqli = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
        $stmt = $mysqli->prepare("SELECT * FROM user WHERE userEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $mysqli->close();
        return new User($user['userID'], $user['userEmail'], $user['userApiKey'], $user['userToken'], $user['userTokenExpire']);
    }

    /**
     * @test
     */
    public function testSaveUserAndApiKeyInsertsNewUserWhenUserExistsInDB(): void
    {
        $email = 'test@example.com';
        $apiKey = "24e9a3dea44346393f632e4161bc83e6";

        $user = $this->dataBaseHandler->saveUserAndApiKeyInDB($email, $apiKey);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($apiKey, $user->getApiKey());
    }

    /**
     * @test
     */
    public function testSaveUserAndApiKeyInsertsNewUserWhenUserNotExistsInDB(): void
    {
        $email = 'test2@example.com';
        $apiKey = "24e9a3dea44346393f632e4161bc83e6";

        $user = $this->dataBaseHandler->saveUserAndApiKeyInDB($email, $apiKey);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($apiKey, $user->getApiKey());

        $this->deleteUserByEmail($email);
    }

    /**
     * @test
     */
    public function testCheckUserExistsInDB(): void
    {
        $email = 'test@example.com';
        $apiKey = "24e9a3dea44346393f632e4161bc83e6";

        $user = $this->dataBaseHandler->checkUserExistsInDB($email, $apiKey);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($apiKey, $user->getApiKey());
    }

    /**
     * @test
     */
    public function testCheckUserExistsInDBWhenEmailNotExists(): void
    {
        $this->expectException(DBException::class);
        $this->expectExceptionMessage('The email must be a valid email address');

        $email = 'test2@example.com';
        $apiKey = "24e9a3dea44346393f632e4161bc83e6";

        $this->dataBaseHandler->checkUserExistsInDB($email, $apiKey);
    }

    /**
     * @test
     */
    public function testCheckUserExistsInDBWhenApiKeyIsWrong(): void
    {
        $this->expectException(ApiKeyException::class);
        $this->expectExceptionMessage('Unauthorized. API access token is invalid.');

        $email = 'test@example.com';
        $apiKey = "24e9a3dea44346393f63";

        $this->dataBaseHandler->checkUserExistsInDB($email, $apiKey);
    }

    /**
     * @test
     */
    public function testInsertTokenIntoDB(): void
    {
        $userId = 9;
        $email = 'test@example.com';
        $apiKey = "24e9a3dea44346393f632e4161bc83e6";
        $user = new User($userId, $email, $apiKey, "", 0);

        $this->dataBaseHandler->insertTokenIntoDB($user, $apiKey);
        $insertResult = $this->selectUserByEmail($email);

        $this->assertInstanceOf(User::class, $insertResult);
        $this->assertEquals($email, $insertResult->getEmail());
        $this->assertEquals($apiKey, $insertResult->getApiKey());
        $this->assertEquals($userId, $insertResult->getUserId());
        $this->assertEquals($apiKey, $insertResult->getToken());
    }

    /**
     * @test
     */
    public function testVerifyTokenWhenTokenExists(): void
    {
        $userId = 9;
        $email = 'test@example.com';
        $apiKey = "24e9a3dea44346393f632e4161bc83e6";
        $token = "24e9a3dea44346393f632e4161bc83e6";

        $user = $this->dataBaseHandler->verifyToken($token);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->getUserId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($apiKey, $user->getApiKey());
        $this->assertEquals($apiKey, $user->getToken());
    }

    /**
     * @test
     */
    public function testVerifyTokenWhenTokenNotExists(): void
    {
        $this->expectException(ApiKeyException::class);
        $this->expectExceptionMessage('Unauthorized. Token is invalid or expired.');
        $token = "24e9a3dea44346393f632e43e6";

        $this->dataBaseHandler->verifyToken($token);
    }

    /**
     * @test
     */
    public function testGetTwitchUserFromDB(): void
    {
        $twitchUser = $this->dataBaseHandler->getTwitchUserFromDB();

        $this->assertInstanceOf(TwitchUser::class, $twitchUser);
        $this->assertEquals(1, $twitchUser->getTokenID());
    }

    /**
     * @test
     */
    public function testGetStreamerFromDB(): void
    {
        $streamer = $this->dataBaseHandler->getStreamerFromDB(1);

        $this->assertInstanceOf(Streamer::class, $streamer);
        $this->assertEquals("1", $streamer->getStreamerId());
        $this->assertEquals("elsmurfoz", $streamer->getLogin());
        $this->assertEquals("elsmurfoz", $streamer->getDisplayName());
        $this->assertEquals("", $streamer->getType());
        $this->assertEquals("", $streamer->getBroadcasterType());
        $this->assertEquals("", $streamer->getDescription());
        $this->assertEquals("https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png", $streamer->getProfileImageUrl());
        $this->assertEquals("", $streamer->getOfflineImageUrl());
        $this->assertEquals("0", $streamer->getViewCount());
        $this->assertEquals("2007-05-22T10:37:47Z", $streamer->getCreatedAt());
    }

    /**
     * @test
     */
    public function testInsertStreamerIntoDB(): void
    {
        $streamer = new Streamer(
            "4",
            "elsmurfoz",
            "elsmurfoz",
            "",
            "",
            "",
            "https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png",
            "",
            "0",
            "2007-05-22T10:37:47Z"
        );
        $this->dataBaseHandler->insertStreamerIntoDB($streamer);
        $streamerResult = $this->dataBaseHandler->getStreamerFromDB(4);
        $this->dataBaseHandler->deleteTestStreamerFromDB();
        $this->assertInstanceOf(Streamer::class, $streamerResult);
        $this->assertEquals("4", $streamerResult->getStreamerId());
        $this->assertEquals("elsmurfoz", $streamerResult->getLogin());
        $this->assertEquals("elsmurfoz", $streamerResult->getDisplayName());
        $this->assertEquals("", $streamerResult->getType());
        $this->assertEquals("", $streamerResult->getBroadcasterType());
        $this->assertEquals("", $streamerResult->getDescription());
        $this->assertEquals("https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png", $streamer->getProfileImageUrl());
        $this->assertEquals("", $streamerResult->getOfflineImageUrl());
        $this->assertEquals("0", $streamerResult->getViewCount());
        $this->assertEquals("2007-05-22T10:37:47Z", $streamerResult->getCreatedAt());
    }
}
