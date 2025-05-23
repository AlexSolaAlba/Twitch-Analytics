<?php

namespace TwitchAnalytics\Controllers\Enriched;

use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class EnrichedValidator extends Validator
{
    public function validateLimit(int|string $limit): void
    {
        if (!$this->checkLimit($limit)) {
            throw new ValidationException('Invalid or missing limit parameter.');
        }
    }

    private function checkLimit(int|string $limit): false|int
    {
        return preg_match("/[0-9]/", $limit);
    }
}
