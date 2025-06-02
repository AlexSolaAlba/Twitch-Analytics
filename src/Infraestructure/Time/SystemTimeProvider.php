<?php

namespace TwitchAnalytics\Infraestructure\Time;

use DateTime;
use TwitchAnalytics\Domain\Time\TimeProviderInterface;

class SystemTimeProvider implements TimeProviderInterface
{
    public function now(): int
    {
        return time();
    }
}
