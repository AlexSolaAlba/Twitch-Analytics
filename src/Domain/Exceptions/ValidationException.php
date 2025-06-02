<?php

namespace TwitchAnalytics\Domain\Exceptions;

class ValidationException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
