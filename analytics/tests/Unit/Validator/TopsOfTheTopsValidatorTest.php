<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsValidator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class TopsOfTheTopsValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
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
