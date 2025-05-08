<?php

namespace TwitchAnalytics\Controllers;

use TwitchAnalytics\Domain\Exceptions\ApplicationException;

class ValidationException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
