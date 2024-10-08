<?php

namespace Startie;

class App
{
    /**
     * @var string App's root path
     */
    public static string $root;

    public static float $initializedAt;

    public static function init(string $root)
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

    /**
     * Returns absolute path
     */
    public static function path(string $path = ""): string
    {
        return self::$root . "/$path";
    }

    public static function getCurrentVersion()
    {
        $version = exec('git describe --tags --abbrev=0');
        $version = trim($version);

        return $version;
    }

    public static function getLastUpdateDate()
    {
        $commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
        $commitDate->setTimezone(new \DateTimeZone('UTC'));

        $date = $commitDate->format('Y-m-d H:i:s');

        return $date;
    }
}