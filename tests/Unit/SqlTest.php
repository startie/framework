<?php

declare(strict_types=1);

use Startie\Sql;
use PHPUnit\Framework\TestCase;

final class SqlTest extends TestCase
{
    public function test_q(): void
    {
        $sql = Sql::q("count");

        $this->assertSame($sql, "`count`");
    }

    public function test_startsWithBacktick(): void
    {
        $result = Sql::startsWithBacktick("`count`");

        $this->assertSame($result, true);
    }

    public function test_ts(): void
    {
        $sql = Sql::ts();

        $this->assertSame($sql, "`UTC_TIMESTAMP()`");
    }

    public function test_like(): void
    {
        $sql = Sql::like("ll");

        $this->assertSame($sql, "`LIKE '%ll%'`");
    }
}