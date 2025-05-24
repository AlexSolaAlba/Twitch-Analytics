<?php

namespace Unit\Service;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\UserService;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreamer\FakeApiTwitchStreamer;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\StreamerRepository;

class UserServiceTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    private UserService $userService;
    private DatabaseHandler $dataBaseHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $apiStreamer = new FakeApiTwitchStreamer();
        $this->dataBaseHandler = new DataBaseHandler();
        $streamerRepository = new StreamerRepository($this->dataBaseHandler, $apiStreamer);
        $this->userService = new UserService($streamerRepository);
    }

    /**
     * @test
     */
    public function givenStreamerIdThatExistsInDBReturnsStreamerInfo(): void
    {
        $response = $this->userService->returnStreamerInfo(1, "24e9a3dea44346393f632e4161bc83e6");

        $this->assertEquals([
            'id' => '1',
            'login' => 'elsmurfoz',
            'display_name' => 'elsmurfoz',
            'type' => '',
            'broadcaster_type' => '',
            'description' => '',
            'profile_image_url' => 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png',
            'offline_image_url' => '',
            'view_count' => '0',
            'created_at' => '2007-05-22T10:37:47Z',
        ], $response);
    }
}
