<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Controllers\Enriched\EnrichedValidator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class EnrichedValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    private EnrichedValidator $enrichedValidator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->enrichedValidator = new EnrichedValidator();
    }

    /**
     * @test
     */
    public function givenWrongLimitReturnsAnException()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid or missing limit parameter.');
        $this->enrichedValidator->validateLimit("null");
    }
}
