<?php

namespace Startie;

class App
{
    public static $root;
    public static float $initializedAt;

    public static function init($root)
    {
        self::$root = $root;
        self::$initializedAt = microtime(true);

        // Autoloading

        require "$root/vendor/autoload.php";

        // App constants

        require "$root/backend/Config/Common/App.php";

        // .env

        $dotenv = \Dotenv\Dotenv::createImmutable($root);
        $dotenv->load();
    }

    public static function path($path = "")
    {
        return self::$root . "/$path";
    }
}
