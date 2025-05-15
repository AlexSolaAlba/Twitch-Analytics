<?php

namespace TwitchAnalytics\Domain\Exceptions;

use TwitchAnalytics\Domain\Exceptions\ApplicationException;

class TwitchApiException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
