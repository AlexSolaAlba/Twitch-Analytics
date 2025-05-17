<?php

namespace TwitchAnalytics\Controllers\Validator;

use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;

class Validator
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

    private function checkEmail($email): false|int
    {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
    }

    public function validateToken(?string $authorization): string
    {
        if ($authorization && str_starts_with($authorization, 'Bearer ')) {
            return substr($authorization, 7);
        }

        throw new ApiKeyException('Unauthorized. Token is invalid or expired.');
    }
}
