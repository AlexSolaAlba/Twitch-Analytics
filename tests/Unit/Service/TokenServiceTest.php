<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Mockery;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use TwitchAnalytics\Application\Services\TokenService;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;

class TokenServiceTest extends TestCase
{
    private TokenService $tokenService;

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
        $this->tokenService  = new TokenService($keyGenerator, $userRepository);
    }

    /**
     * @test
     * @throws RandomException
     */
    public function givenAnEmailAndKeyReturnsToken(): void
    {
        $response = $this->tokenService->generateToken("test@example.com", "24e9a3dea44346393f632e4161bc83e6");

        $this->assertEquals(['token' => "24e9a3dea44346393f632e4161bc83e6"], $response);
    }
}
