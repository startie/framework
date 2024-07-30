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
     * Used for raw SQL expressions
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

    public static function isNull()
    {
        return Sql::q(
            'IS NULL'
        );
    }

    public static function isNotNull()
    {
        return Sql::q(
            'IS NOT NULL'
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

    /**
     * @tested
     */
    public static function regexp($pattern)
    {
        $query = "";

        $query .= "REGEXP ";
        $query .= "'"; // delimeter for pattern
        $query .= $pattern;
        $query .= "'"; // delimeter for pattern

        $query = Sql::q($query);

        return $query;
    }
}