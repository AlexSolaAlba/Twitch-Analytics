<?php

namespace TwitchAnalytics\Domain\Exceptions;

class ApplicationException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
