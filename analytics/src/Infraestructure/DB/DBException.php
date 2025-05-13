<?php

namespace TwitchAnalytics\Infraestructure\DB;

use TwitchAnalytics\Domain\Exceptions\ApplicationException;

class DBException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
