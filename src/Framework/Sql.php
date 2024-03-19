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
     * 
     * @tested
     */
    public static function q($expression)
    {
        return "`$expression`";
    }

    /**
     * @tested
     */
    public static function hasBacktick($val)
    {
        if (str_contains($val ?? '', '`')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @tested
     */
    public static function startsWithBacktick($val)
    {
        $val = $val ?? '';
        $val = strval($val);

        if (strpos($val, '`') === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @tested
     */
    public static function ts()
    {
        return Sql::q(
            MySql::ts()
        );
    }

    /**
     * @tested
     */
    public static function like($value)
    {
        return Sql::q(
            StatementBuilder::like($value)
        );
    }
}