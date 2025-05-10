<?php

namespace TwitchAnalytics\Tests\Unit;

use Laravel\Lumen\Testing\TestCase;

class TokenValidatorTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }
}
