<?php

namespace TwitchAnalytics\Tests\Unit;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\Register\RegisterValidator;

use TwitchAnalytics\Controllers\ValidationException;

use function PHPUnit\Framework\assertEquals;

class RegisterValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    private RegisterValidator $registerValidator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->registerValidator = new RegisterValidator();
    }

    /**
     * @test
     */
    public function notGivenAnEmailReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email is mandatory');
        $this->registerValidator->validate(null);
    }

    /**
     * @test
     */
    public function whenSanitizedEmailIsEmptyReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email must be a valid email address');
        $this->registerValidator->validate("=)?");
    }

    /**
     * @test
     */
    public function whenSanitizedEmailIsWrongReturnsAnException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email must be a valid email address');
        $this->registerValidator->validate("shgfdjshfdjus.com");
    }

    /**
     * @test
     */
    public function givenRightEmailReturnsEmail(): void
    {
        assertEquals("hola@gmail.com",$this->registerValidator->validate("hola@gmail.com"));
    }
}
