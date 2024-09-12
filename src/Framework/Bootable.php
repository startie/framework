<?php

namespace Startie;

trait Bootable
{
    public static $isBooted = false;
    public static $config;

    abstract static function boot();

    /**
     * Check if was booted.
     * 
     * Throws an exception if the class was never booted.
     *
     * @return void
     */
    public static function requireBoot()
    {
        if (!self::$isBooted) {
            throw new \Startie\Exception(__CLASS__ . " was never booted");
        }
    }
}