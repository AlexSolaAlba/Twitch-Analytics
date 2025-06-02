<?php

namespace Integration\DB;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Models\User;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Exceptions\DBException;

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

        $user = $this->dataBaseHandler->checkUserExistsInDB($email, $apiKey);
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

        $user = $this->dataBaseHandler->checkUserExistsInDB($email, $apiKey);
    }
}
