<?php

namespace Startie;

class Logger
{
    use \Startie\Bootable;

    public static function boot()
    {
        self::$isBooted = true;
        self::loadConfig();
    }

    public static function loadConfig()
    {
        self::$config = \Startie\Config::get("Logs");

        /*
            Validate config
        */

        // - storage type
        switch (self::$config['storage']['type']) {
            case "db":
                break;
            default:
                throw new Exception(
                    "Unsupported storage type for logs: "
                        . self::$config['storage']['type']
                );
        }
    }

    public static function toFile($text)
    {
        $text = (new \DateTime())->format(\DATE_ATOM) . PHP_EOL . $text;
        error_log($text, 3, \Startie\App::path("/php-error.log"));
    }

    public static function storage()
    {
        $name = Logger::$config['storage']['name']; // e.g 'logs', 'common', etc.
        if (!empty(Db::$connections)) {
            $connection = Db::$connections[$name];
        } else {
            throw new \Startie\Exception('No DB connections avaliable');
        }

        return $connection;
    }
}