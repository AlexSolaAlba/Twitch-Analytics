<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class ValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    private Validator $validator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    /**
     * @test
     */
    public function notGivenAnEmailReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email is mandatory');
        $this->validator->validateEmail(null);
    }

    /**
     * @test
     */
    public function whenSanitizedEmailIsEmptyReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email must be a valid email address');
        $this->validator->validateEmail("=)?");
    }

    /**
     * @test
     */
    public function whenSanitizedEmailIsWrongReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email must be a valid email address');
        $this->validator->validateEmail("shgfdjshfdjus.com");
    }

    /**
     * @test
     */
    public function givenRightEmailReturnsEmail(): void
    {
        $this->assertEquals("hola@gmail.com", $this->validator->validateEmail("hola@gmail.com"));
    }

    /**
     * @test
     */
    public function givenRightAuthorizationReturnsToken(): void
    {
        $this->assertEquals("jshgfdsjh", $this->validator->validateToken("Bearer jshgfdsjh"));
    }

    /**
     * @test
     */
    public function givenNoAuthorizationReturnsAnException(): void
    {
        $this->expectException(ApiKeyException::class);
        $this->expectExceptionMessage('Unauthorized. Token is invalid or expired.');
        $this->validator->validateToken("");
    }

    /**
     * @test
     */
    public function givenWrongAuthorizationReturnsAnException(): void
    {
        $this->expectException(ApiKeyException::class);
        $this->expectExceptionMessage('Unauthorized. Token is invalid or expired.');
        $this->validator->validateToken("Beare");
    }
}
