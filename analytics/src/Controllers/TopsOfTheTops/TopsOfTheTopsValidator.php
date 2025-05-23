<?php

namespace TwitchAnalytics\Controllers\TopsOfTheTops;

use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class TopsOfTheTopsValidator extends Validator
{
    public function validateSince(int|string|null $since): void {
        if(isset($since)) {
            if (!$this->checkSince($since)) {
                throw new ValidationException('Invalid since parameter.');
            }
        }
    }

    private function checkSince(int|string $since): false|int
    {
        return preg_match("/[0-9]/", $since);
    }
}
