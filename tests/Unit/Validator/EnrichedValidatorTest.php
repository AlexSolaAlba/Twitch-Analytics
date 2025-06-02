<?php

namespace TwitchAnalytics\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Controllers\Enriched\EnrichedValidator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class EnrichedValidatorTest extends TestCase
{
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
