<?php

namespace Integration\Controller;

use Laravel\Lumen\Testing\TestCase;

class StreamsControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
}
