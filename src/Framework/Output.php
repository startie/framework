<?php

namespace Startie;

class Output
{
    use \Startie\Bootable;

    public static function boot(): void
    {
        self::$isBooted = true;
        self::loadConfig();
    }

    public static function loadConfig(): void
    {
        try {
            self::$config = \Startie\Config::get("Output");
        } catch (\Exception $e) {
        }
    }

    public static function json(array $data): string
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