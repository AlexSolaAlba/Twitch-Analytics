<?php

namespace TwitchAnalytics\Controllers\Token;
use TwitchAnalytics\Controllers\ValidationException;
class TokenValidator
{
    public function validateEmail(?string $email): string
    {
        if (!isset($email)) {
            throw new ValidationException('The email is mandatory');
        }

        $sanitizedEmail = strip_tags($email);
        $sanitizedEmail = htmlspecialchars($sanitizedEmail, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $sanitizedEmail = filter_var($sanitizedEmail, FILTER_SANITIZE_EMAIL);

        if (empty($sanitizedEmail)) {
            throw new ValidationException('The email must be a valid email address');
        }

        if (!$this->checkEmail($email)) {
            throw new ValidationException('The email must be a valid email address');
        }

        return $email;
    }
    public function validateKey(?string $key): string
    {
        if (!isset($key)) {
            throw new ValidationException('The email is mandatory');
        }

        $sanitizedKey = strip_tags($key);
        $sanitizedKey = htmlspecialchars($sanitizedKey, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $sanitizedEmail = filter_var($sanitizedKey, FILTER_SANITIZE_EMAIL);

        if (empty($sanitizedKey)) {
            throw new ValidationException('The key must be a valid key');
        }

        if (!$this->checkKey($key)) {
            throw new ValidationException('The key must be a valid key');
        }

        return $key;
    }

    private function checkEmail($email): false|int
    {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
    }

    private function checkKey(string $key)
    {
        return preg_match('/^[a-f0-9]{32}$/', $key);
    }
}
