<?php

namespace Startie;

class Schema
{
    public static function hasBt($val)
    {
        if (strpos($val, '`') === 0) {
            return true;
        } else {
            return false;
        }
    }

    // #todo: transfer to the new class Mysql
    // alnum = letter or digit

    public static function regexpSearch($field, $query)
    {
        $query = mb_strtolower($query);

        $regexp = "[[:alpha:] -]*";

        $result = "";
        $result .= "`";
        $result .= "REGEXP '\"{$field}\":\"{$regexp}{$query}{$regexp}'";
        $result .= "`";

        return $result;
    }

    // #todo: transfer to the new class Mysql

    public static function ts()
    {
        return '`UTC_TIMESTAMP()`';
    }
}
