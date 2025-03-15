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
    public static function q(string $expression): string
    {
        return "`$expression`";
    }

    /**
     * @tested
     */
    public static function hasBacktick(string|int|float $val): bool
    {
        $val = strval($val);

        if (str_contains($val, '`')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @tested
     */
    public static function startsWithBacktick(string|int|float $value): bool
    {
        $value = strval($value);

        if (strpos($value, '`') === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @tested
     */
    public static function ts(): string
    {
        return Sql::q(
            MySql::ts()
        );
    }

    public static function isNull(): string
    {
        return Sql::q(
            'IS NULL'
        );
    }

    public static function isNotNull(): string
    {
        return Sql::q(
            'IS NOT NULL'
        );
    }

    /**
     * @tested
     */
    public static function like(string|int|float $value): string
    {
        return Sql::q(
            StatementBuilder::like($value)
        );
    }

    /**
     * @tested
     */
    public static function regexp(string $pattern): string
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