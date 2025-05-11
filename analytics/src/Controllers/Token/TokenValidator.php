<?php

namespace TwitchAnalytics\Controllers\Token;

use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Controllers\Validator\Validator;

class TokenValidator extends Validator
{
    public function validateKey(?string $key): string
    {
        if (!isset($key)) {
            throw new ValidationException('The api_key is mandatory');
        }

        $sanitizedKey = strip_tags($key);
        $sanitizedKey = htmlspecialchars($sanitizedKey, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $sanitizedKey = filter_var($sanitizedKey, FILTER_SANITIZE_EMAIL);

        if (empty($sanitizedKey)) {
            throw new ValidationException('The api_key must be a valid key');
        }

        if (!$this->checkKey($key)) {
            throw new ValidationException('The api_key must be a valid key');
        }

        return $key;
    }

    private function checkKey(string $key): false|int
    {
        return preg_match('/^[a-f0-9]{32}$/', $key);
    }
}
