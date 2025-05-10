<?php

namespace TwitchAnalytics\Tests\Unit;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Controllers\ValidationException;

use function PHPUnit\Framework\assertEquals;

class RegisterValidatorTest extends TestCase
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
        assertEquals("hola@gmail.com", $this->validator->validateEmail("hola@gmail.com"));
    }
}
