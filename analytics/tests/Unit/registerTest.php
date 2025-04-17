<?php

declare(strict_types=1);

namespace tests\Unit;

use PHPUnit\Framework\TestCase;

include_once "analytics/src/register.php";

class RegisterTest extends TestCase
{
    /**
     * @test
     */
    public function givenValidEmailReturns1()
    {
        $this->assertEquals(1, comprobarEmail("test@test.com"));
    }
    /**
     * @test
     */
    public function givenInvalidEmailReturns0()
    {
        $this->assertEquals(0, comprobarEmail("testtest.com"));
    }
}
