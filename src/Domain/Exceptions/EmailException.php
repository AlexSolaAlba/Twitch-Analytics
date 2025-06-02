<?php

namespace TwitchAnalytics\Domain\Exceptions;

class EmailException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
