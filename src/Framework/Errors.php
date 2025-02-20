<?php

declare(strict_types=1);

namespace Startie;

use Startie\Logger;

class Errors
{
    use \Startie\Bootable;

    public static string $handler;

    public static function boot(): void
    {
        self::$isBooted = true;
        self::loadConfig();
    }

    public static function loadConfig(): void
    {
        $defaultConfig = [
            'ini' => [
                'display_errors' => 1,
                'display_startup_errors' => 1,
                'ignore_repeated_errors' => 1,
            ],
            'error_reporting' => 0,

            'handler' => 'Startie',
            'handlerEditor' => null,
            'render' => true,
            'log' => false,
        ];

        $stage = strtolower(\Startie\Config::$stage);
        $machine = strtolower(\Startie\Config::$machine);

        $configPath = App::path("backend/Config/Errors/{$stage}_{$machine}.php");
        if (is_file($configPath)) {
            self::$config = require $configPath;
            self::$config = self::$config + $defaultConfig;

            self::$config['accessToken']['key'] =
                $_ENV['ERRORS_RENDER_ACCESS_TOKEN_KEY'] ?? null;
            self::$config['accessToken']['value'] =
                $_ENV['ERRORS_RENDER_ACCESS_TOKEN_VALUE'] ?? null;
        } else {
            throw new Exception(
                "Config path for `Errors` was not found: "
                    . $configPath
            );
        }
    }

    /**
     * This method loads config file a set everything up
     */
    public static function init(): void
    {
        /*
            Set ini
        */

        foreach (Errors::$config['ini'] as $option => $value) {
            ini_set($option, $value);
        }

        /*
            Set handler
        */

        Errors::$handler = match (Errors::$config['handler']) {
            "Whoops" => "Whoops",
            default => "Startie",
        };

        Errors::registerHandlers();

        /*
            Turn on reporting
        */

        error_reporting(Errors::$config['error_reporting']);
    }

    /**
     * Register handlers
     */
    public static function registerHandlers(): void
    {
        if (Errors::$handler === "Whoops") {
            $handler = new \Whoops\Handler\PrettyPageHandler;
            $handler->setEditor(
                Errors::$config['handlerEditor']
            );
            $whoops = new \Whoops\Run;
            $whoops->pushHandler($handler);
            $whoops->register();
        } else if (Errors::$handler === "Startie") {
            set_error_handler("Startie\Errors::errorHandler");
            set_exception_handler("Startie\Errors::exceptionHandler");
            register_shutdown_function("Startie\Errors::shutdownFunction");
        }
    }

    /**
     * Method that converts all errors to exceptions
     */
    public static function errorHandler(
        int $level,
        string $message,
        string $file = '',
        int $line = 0
    ): void {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Method to call when an uncaught exception occurs
     *
     * @param mixed $inputException
     * @return void
     */
    public static function exceptionHandler($inputException)
    {
        /*
            Log in storage
        */

        if (Errors::$config['log']) {
            if (isset(Logger::$config['model'])) {
                $model = Logger::$config['model'];
                $LogId = $model::generateFromException($inputException);
            } else {
                throw new Exception('No model set in Logger config');
            }
        }

        /*
            Collect errors
        */

        if (Errors::$handler === 'Startie') {
            if (ini_get('display_errors')) {
                if (isset($LogId)) {
                    $errorHTML = Errors::make(
                        $inputException,
                        "Log #$LogId was generated."
                    );
                } else {
                    $errorHTML = Errors::make(
                        $inputException,
                        "No log was generated."
                    );
                }
            } else {
                $errorHTML = Errors::make(
                    "Unknown error has been occurred"
                );
            }
        }

        /*
            Display
        */

        ob_get_clean();

        if (
            // If `render` in config set to `true`
            (Errors::$config['render'] ?? false)
            ||
            // If accessToken was provided
            In::get(
                Errors::$config['accessToken']['key']
            ) === Errors::$config['accessToken']['value']
        ) {
            if (Errors::$handler === 'Startie') {
                echo $errorHTML;
            }
        } else {
            if (Errors::$handler === 'Startie') {
                $errorHTML = Errors::make('An unexpected error #0 has happened.');
                echo $errorHTML;
            }
        }
    }

    public static function shutdownFunction(): void
    {
        // If there was an error and it was fatal
        if (
            $error = error_get_last()
            and
            $error['type']
            &
            (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)
        ) {
            // Clean buffer & don't show default error message
            if (ob_get_length() > 0) {
                ob_end_clean();
            }

            // Show error message for production
            if ($_ENV["MODE_DEV"] === 0) {
                $errorText = "Please send a link to the page to support";
                echo Errors::make($errorText);
            }

            // Show error message in dev mode
            elseif ($_ENV['MODE_DEV'] === 1) {
                $errorText = $error['message'] . " "
                    . $error['file']
                    . " [" . $error['line'] . "]";
                echo Errors::make($errorText);
            }

            // Show only error ID
            else {
                Errors::errorHandler(
                    $error['type'],
                    $error['message'],
                    $error['file'],
                    $error['line']
                );
                echo Errors::make("#unknown id error");
            }
        } else {
            // Send & turn off the buffer
            if (ob_get_length() > 0) {
                ob_end_flush();
            }
        }
    }

    public static function make(string $message, string $addition = ""): string
    {
        $html = "
            <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' />
            <div style='margin: 50px; font-family: Menlo, Courier New;'>
                <h1>Error</h1>
                <div style='white-space: pre-wrap; font-size: 16px;'>{$message}</div>
                <br>
                <div class='margin-top:30px'>{$addition}</div>
            </div>
        ";

        return $html;
    }
}