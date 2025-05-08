<?php

namespace TwitchAnalytics\Controllers\Register;

use TwitchAnalytics\Controllers\ValidationException;

class RegisterValidator
{
    public function validate(?string $email): string
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

        if (!$this->comprobarEmail($email)) {
            throw new ValidationException('The email must be a valid email address');
        }

        return $email;
    }

    private function comprobarEmail($email): false|int
    {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
    }
}
