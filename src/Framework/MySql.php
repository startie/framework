<?php

namespace Startie;

/**
 * Generate MySQL specific queries
 */
class MySql
{
    public static function ts()
    {
        return "UTC_TIMESTAMP()";
    }
}