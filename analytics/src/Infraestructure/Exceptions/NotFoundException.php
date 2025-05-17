<?php

namespace TwitchAnalytics\Infraestructure\Exceptions;

use TwitchAnalytics\Domain\Exceptions\ApplicationException;

class NotFoundException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
