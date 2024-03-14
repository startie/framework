<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startie\MySql;

final class MySqlTest extends TestCase
{
    public function test_ts(): void
    {
        $sql = MySql::ts();

        $this->assertSame($sql, "UTC_TIMESTAMP()");
    }
}