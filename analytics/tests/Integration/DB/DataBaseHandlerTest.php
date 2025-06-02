<?php

namespace Integration\DB;

use PHPUnit\Framework\TestCase;
use Random\RandomException;
use TwitchAnalytics\Domain\Models\User;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

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
     * @throws RandomException
     */
    public function testSaveUserAndApiKeyInsertsNewUser()
    {
        $email = 'test@example.com';
        $apiKey = bin2hex(random_bytes(16));

        $user = $this->dataBaseHandler->saveUserAndApiKeyInDB($email, $apiKey);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($apiKey, $user->getApiKey());

        $this->deleteUserByEmail($email);
    }
}
