<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class UserValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    private UserValidator $userValidator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->userValidator = new UserValidator();
    }

    /**
     * @test
     */
    public function givenWrongIdReturnsAnException()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid or missing id parameter.');
        $this->userValidator->validateUserId("null");
    }
}
