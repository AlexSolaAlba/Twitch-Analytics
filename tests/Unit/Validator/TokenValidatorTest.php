<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Controllers\Token\TokenValidator;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class TokenValidatorTest extends TestCase
{
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
        $this->expectExceptionMessage('The api_key is mandatory');
        $this->tokenValidator->validateKey(null);
    }

    /**
     * @test
     */
    public function whenSanitizedKeyIsEmptyReturnsAnException(): void
    {
        $this->expectException(ApiKeyException::class);
        $this->expectExceptionMessage('Unauthorized. API access token is invalid.');
        $this->tokenValidator->validateKey("=)?");
    }

    /**
     * @test
     */
    public function whenSanitizedKeyIsWrongReturnsAnException(): void
    {
        $this->expectException(ApiKeyException::class);
        $this->expectExceptionMessage('Unauthorized. API access token is invalid.');
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
