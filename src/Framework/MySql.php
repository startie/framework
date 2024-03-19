<?php

declare(strict_types=1);

namespace Startie;

/**
 * Generate MySQL specific queries
 */
class MySql
{
    /**
     * @tested
     */
    public static function ts()
    {
        return "UTC_TIMESTAMP()";
    }
}