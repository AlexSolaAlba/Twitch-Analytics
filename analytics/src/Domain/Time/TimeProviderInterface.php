<?php

namespace TwitchAnalytics\Domain\Time;

interface TimeProviderInterface
{
    public function now(): \DateTime;
}
