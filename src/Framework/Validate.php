<?php

namespace Startie;

class Validate
{
    
    /*
    
            Boolean returns
    
    */
    
    public static function str($value): bool
    {
        return is_string($value);
    }

    /**
     * Alias for `Validate::str()`
     */
    public static function string($value): bool
    {
        return Validate::str($value);
    }

    public static function int($value): bool
    {
        return is_int($value);
    }

    /**
     * Alias for `Validate::int()`
     */
    public static function integer($value): bool
    {
        return Validate::int($value);
    }

    /**
     * Alias for `Validate::int()`
     */
    public static function number($value): bool
    {
        return Validate::int($value);
    }

    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    /*
        
            Based on `filter_var()`

    */

    public static function boolean($value): bool|null
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
    public static function float($value): float|false
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

    public static function intStr($value): int|false
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public static function ip($value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public static function mac($value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_MAC);
    }

    public static function email($value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function url($value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public static function regexp($value): string|false
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
    public static function bool($value): int
    {
        return intval(
            filter_var($value, FILTER_VALIDATE_BOOLEAN)
        );
    }
}