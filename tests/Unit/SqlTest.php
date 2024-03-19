<?php

declare(strict_types=1);

use Startie\Sql;
use PHPUnit\Framework\TestCase;

final class SqlTest extends TestCase
{
    /*
        ::q()
    */

    public function test_q(): void
    {
        $sql = Sql::q("count");

        $this->assertSame($sql, "`count`");
    }

    /*
        ::hasBacktick()
    */

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

    /*
        ::startsWithBacktick()
    */
    public function test_starts_with_backtick(): void
    {
        $result = Sql::startsWithBacktick("`count`");

        $this->assertSame($result, true);
    }

    /*
        ::ts()
    */

    public function test_ts(): void
    {
        $sql = Sql::ts();

        $this->assertSame($sql, "`UTC_TIMESTAMP()`");
    }

    /*
        ::like()
    */

    public function test_like(): void
    {
        $sql = Sql::like("ll");

        $this->assertSame($sql, "`LIKE '%ll%'`");
    }
}