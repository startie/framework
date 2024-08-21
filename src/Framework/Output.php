<?php

namespace Startie;

class Output
{
    use \Startie\Bootable;

    public static function boot()
    {
        self::$isBooted = true;
        self::loadConfig();
    }

    public static function loadConfig()
    {
        try {
            self::$config = \Startie\Config::get("Output");
        } catch (\Exception $e) {
            
        }
    }

    public static function json($data): string
    {
        return json_encode($data);
    }

    public static function plain(string $data): string
    {
        return $data;
    }

    /**
     * Send header with an HTTP error
     */
    public static function error(int $code): void
    {
        // Can work without boot, it will just show HTTP status
        // self::requireBoot();

        if (isset(self::$config['error'])) {
            $errorConfig = self::$config['error'][$code];

            if (isset($errorConfig['view'])) {
                echo view($errorConfig['view']);
            }
        } else {
            header("HTTP/1.0 $code");
            die();
        }
    }
}