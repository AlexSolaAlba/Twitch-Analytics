<?php

namespace TwitchAnalytics\Controllers\Token;

use TwitchAnalytics\Controllers\Validator\Validator;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\ValidationException;

class TokenValidator extends Validator
{
    public function validateKey(?string $key): string
    {
        if (!isset($key)) {
            throw new ValidationException('The api_key is mandatory');
        }

        $sanitizedKey = strip_tags($key);

        $sanitizedKey = htmlspecialchars($sanitizedKey, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $sanitizedKey = trim($sanitizedKey);

        if (empty($sanitizedKey)) {
            throw new ApiKeyException('Unauthorized. API access token is invalid.');
        }

        if (!$this->checkKey($key)) {
            throw new ApiKeyException('Unauthorized. API access token is invalid.');
        }

        return $key;
    }

    private function checkKey(string $key): false|int
    {
        return preg_match('/^[a-f0-9]{32}$/', $key);
    }
}
