<?php

namespace TwitchAnalytics\Domain\Exceptions;

class ApiKeyException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
