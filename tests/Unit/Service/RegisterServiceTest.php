<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Mockery;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;

class RegisterServiceTest extends TestCase
{
    private RegisterService $registerService;

    /**
     * @throws RandomException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        parent::setUp();
        $keyGenerator = Mockery::mock(RandomKeyGenerator::class);
        $keyGenerator->allows()->generateRandomKey()->andReturns("24e9a3dea44346393f632e4161bc83e6");
        $dataBaseHandler = new DatabaseHandler();
        $userRepository = new UserRepository($dataBaseHandler);
        $this->registerService  = new RegisterService($keyGenerator, $userRepository);
    }

    /**
     * @test
     * @throws RandomException
     */
    public function givenAnEmailReturnsApiKey(): void
    {
        $response = $this->registerService->register("test@example.com");

        $this->assertEquals(['api_key' => "24e9a3dea44346393f632e4161bc83e6"], $response);
    }
}
