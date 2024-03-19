<?php

declare(strict_types=1);

namespace Startie;

use Startie\StatementBuilder;

/**
 * Provides helper methods and wrappers for generating and checking
 * SQL queries in custom flavored backticks syntax
 */
class Sql
{
    /**
     * Wrap expression in backticks quotes (``) 
     * "q" is short for "quotes"
     */
    public static function q($expression)
    {
        return "`$expression`";
    }

    public static function startsWithBacktick($val)
    {
        if (strpos($val ?? '', '`') === 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function hasBacktick($val)
    {
        if (str_contains($val ?? '', '`')) {
            return true;
        } else {
            return false;
        }
    }

    public static function ts()
    {
        return Sql::q(
            MySql::ts()
        );
    }

    public static function like($value)
    {
        return Sql::q(
            StatementBuilder::like($value)
        );
    }
}