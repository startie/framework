<?php

namespace Startie;

class Validate
{

    /*
    
            Boolean returns
    
    */

    public static function str(mixed $value): bool
    {
        return is_string($value);
    }

    /**
     * Alias for `Validate::str()`
     */
    public static function string(mixed $value): bool
    {
        return Validate::str($value);
    }

    public static function int(mixed $value): bool
    {
        return is_int($value);
    }

    /**
     * Alias for `Validate::int()`
     */
    public static function integer(mixed $value): bool
    {
        return Validate::int($value);
    }

    /**
     * Alias for `Validate::int()`
     */
    public static function number(mixed $value): bool
    {
        return Validate::int($value);
    }

    public static function numeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    /*
        
            Based on `filter_var()`

    */

    public static function boolean(mixed $value): bool|null
    {
        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
    }

    /**
     * Even if $value will be a string, float will be returned
     * @tested
     */
    public static function float(mixed $value): float|false
    {
        $temp_var = str_replace(".", ",", $value);

        return filter_var(
            $temp_var,
            FILTER_VALIDATE_FLOAT,
            [
                'options' => [
                    'decimal' => ','
                ]
            ]
        );
    }

    public static function intStr(mixed $value): int|false
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public static function ip(mixed $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public static function mac(mixed $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_MAC);
    }

    public static function email(mixed $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function url(mixed $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public static function regexp(mixed $value, string $regexp): string|false
    {
        return filter_var($value, FILTER_VALIDATE_REGEXP);
    }

    /**
     * @deprecated Use `Validate::boolean()`
     * 
     * Buggy nature
     * `filter_var` returns `null` if it is not boolean
     * but later this `null` will be converted to `false`
     * Therefore some random string, like "php" will pass validation
     * as bool `false`
     * 
     * @tested
     */
    public static function bool(mixed $value): int
    {
        return intval(
            filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }
}