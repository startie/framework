<?php

declare(strict_types=1);

namespace Startie;

class App
{
public static string $DIR_APP;
    public static string $FRONTEND_DIR;
    public static string $BACKEND_DIR;
    public static string $PUBLIC_DIR;
    public static string $STORAGE_DIR;
    public static string $VENDOR_DIR;

    public static string $DATE_TIMEZONE;
    public static string $TIMEZONE;
    public static string $LOCALE;

    public static string $APP_PROTOCOL;
    public static string $URL_APP;
    public static string $PUBLIC_URL;
    public static string $STORAGE_URL;
    /* @deprecated 0.46.0 */
    public static string $NODE_MODULES_URL;
        

    /**
     * @var string App's root path
     */
    public static string $root;

    public static float $initializedAt;

    public static function init(string $root): void
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

    /**
     * Returns empty string if version was not found
     */
    public static function getCurrentVersion(): string
    {
        $version = exec('git describe --tags --abbrev=0');
        $version = trim($version);

        return $version;
    }

    /**
     * Returns empty string if date was not found
     */
    public static function getLastUpdateDate(): string
    {
        $commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
        $commitDate->setTimezone(new \DateTimeZone('UTC'));

        $date = $commitDate->format('Y-m-d H:i:s');

        return $date;
    }
}