<?php

namespace TwitchAnalytics\Controllers\User;

use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class UserValidator extends Validator
{
    public function validateUserId(int|string $userId): void
    {
        if (!$this->checkUserId($userId)) {
            throw new ValidationException('Invalid or missing id parameter.');
        }
    }

    private function checkUserId(int|string $userId): false|int
    {
        return preg_match("/[0-9]/", $userId);
    }
}
