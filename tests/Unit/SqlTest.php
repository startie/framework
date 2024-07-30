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

    /**
     * Test for Sql::ts()
     */
    public function test_ts(): void
    {
        $sql = Sql::ts();

        $this->assertSame($sql, "`UTC_TIMESTAMP()`");
    }

    /**
     * Test for Sql::isNull()
     */
    public function test_isNull(): void
    {
        $sql = Sql::isNull();

        $this->assertSame($sql, "`IS NULL`");
    }

    /**
     * Test for Sql::isNull()
     */
    public function test_isNotNull(): void
    {
        $sql = Sql::isNotNull();

        $this->assertSame($sql, "`IS NOT NULL`");
    }

    /**
     * Test for Sql::like()
     */
    public function test_like(): void
    {
        $sql = Sql::like("ll");

        $this->assertSame($sql, "`LIKE '%ll%'`");
    }

    /**
     * Test for Sql::regexp()
     */
    public function test_regexp(): void
    {
        $expectation = <<<RE
        `REGEXP '"name": ?"[[:alpha:] -]*john[[:alpha:] -]*'`
        RE;

        $name = "john";
        $reality = Sql::regexp(
            <<<RE
			"name": ?"[[:alpha:] -]*{$name}[[:alpha:] -]*
			RE
        );

        $this->assertSame($expectation, $reality);
    }
}