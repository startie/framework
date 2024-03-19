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

    public function test_has_backtick(): void
    {
        $result = Sql::hasBacktick("`count`");

        $this->assertSame($result, true);
    }

    public function test_has_not_backtick(): void
    {
        $result = Sql::hasBacktick("count");

        $this->assertSame($result, false);
    }

    public function test_starts_with_backtick(): void
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