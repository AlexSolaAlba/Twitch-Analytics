<?php

namespace TwitchAnalytics\Tests\Unit;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\Token\TokenValidator;
use TwitchAnalytics\Controllers\ValidationException;

class TokenValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    private TokenValidator $tokenValidator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenValidator = new TokenValidator();
    }

    /**
     * @test
     */
    public function notGivenAKeyReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The key is mandatory');
        $this->tokenValidator->validateKey(null);
    }

    /**
     * @test
     */
    public function whenSanitizedKeyIsEmptyReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The key must be a valid key');
        $this->tokenValidator->validateKey("=)?");
    }

    /**
     * @test
     */
    public function whenSanitizedKeyIsWrongReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The key must be a valid key');
        $this->tokenValidator->validateKey("219naufe2");
    }

    /**
     * @test
     */
    public function givenRightKeyReturnsKey(): void
    {
        $this->assertEquals("24e9a3dea44346393f632e4161bc83e6", $this->tokenValidator->validateKey("24e9a3dea44346393f632e4161bc83e6"));
    }
}
