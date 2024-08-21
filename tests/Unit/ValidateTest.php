<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startie\Validate;

final class ValidateTest extends TestCase
{
    public function testBoolWithSuccess()
    {
        $reality = Validate::bool(true);
        $expectation = 1;

        $this->assertSame($expectation, $reality);
    }

    public function testBoolWithSuccess2()
    {
        $reality = Validate::bool(false);
        $expectation = 0;

        $this->assertSame($expectation, $reality);
    }

    public function testBoolWithBug()
    {
        $reality = Validate::bool("php");
        $expectation = 0;

        $this->assertSame($expectation, $reality);
    }

    public function testBoolean()
    {
        $reality = Validate::boolean(true);
        $expectation = true;

        $this->assertSame($expectation, $reality);
    }

    public function testBoolean2()
    {
        $reality = Validate::boolean(false);
        $expectation = false;

        $this->assertSame($expectation, $reality);
    }

    public function testBooleanWithoutBug()
    {
        $reality = Validate::boolean("php");
        $expectation = null;

        $this->assertSame($expectation, $reality);
    }

    public function testFloatWithSuccess()
    {
        $reality = Validate::float("5.1");
        $expectation = 5.1;

        $this->assertSame($expectation, $reality);
    }
}