<?php

namespace TwitchAnalytics\Domain\Key;

use Random\RandomException;

class RandomKeyGenerator
{
    /**
     * @throws RandomException
     */
    public function generateRandomKey(): string
    {
        return bin2hex(random_bytes(16));
    }
}
