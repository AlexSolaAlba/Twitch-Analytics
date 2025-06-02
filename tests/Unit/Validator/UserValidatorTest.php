<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class UserValidatorTest extends TestCase
{
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
