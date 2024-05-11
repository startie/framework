<?php

namespace Startie;

use Startie\Dump;

class App
{
    public static $root;
    public static float $initializedAt;

    public static function init($root)
    {
        self::$root = $root;
        self::$initializedAt = microtime(true);

        /*
            App constants
        */

        $constantsPath = App::path("backend/Config/Common/App.php");
        if (file_exists($constantsPath)) {
            require $constantsPath;
        } else {
            throw new Exception("Path was not found: " . $constantsPath);
        }

        /*
            Load .env
        */

        $dotenv = \Dotenv\Dotenv::createImmutable($root);
        $dotenv->load();
    }

    public static function path($path = "")
    {
        return self::$root . "/$path";
    }
}
