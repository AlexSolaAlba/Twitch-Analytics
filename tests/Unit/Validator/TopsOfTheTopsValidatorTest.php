<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsValidator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class TopsOfTheTopsValidatorTest extends TestCase
{
    private TopsOfTheTopsValidator $topsValidator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->topsValidator = new TopsOfTheTopsValidator();
    }

    /**
     * @test
     */
    public function givenWrongSinceReturnsAnException()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid since parameter.');
        $this->topsValidator->validateSince("null");
    }
}
