<?php

namespace Startie;

use Startie\Logs;
use Startie\Config;
use Startie\Auth;
use Startie\App;

class Errors
{
    public static $logs;
    public static $handler;

    public static function config()
    {
        /*
            Find config
        */

        $stage = strtolower(Config::$stage);
        $machine = strtolower(Config::$machine);
        $Config_Errors_path = App::path("backend/Config/Errors/{$stage}_{$machine}.php");
        $Config_Errors = require $Config_Errors_path;

        /*
            Set ini
        */

        foreach ($Config_Errors['ini'] as $setting => $value) {
            ini_set($setting, $value);
        }

        /*
            Set logs settings
        */

        self::$logs = $Config_Errors['logs'];

        /*
            Set handler
        */

        if (isset($Config_Errors['handler'])) {
            if ($Config_Errors['handler'] == 'Whoops') {
                $handler = new \Whoops\Handler\PrettyPageHandler;
                $handler->setEditor(
                    $Config_Errors['handlerEditor']
                );
                $whoops = new \Whoops\Run;
                $whoops->pushHandler($handler);
                $whoops->register();
                self::$handler = 'Whoops';
            } else {
                self::$handler = 'Startie';
            }
        }

        /*
            Turn on reporting
        */

        error_reporting($Config_Errors['error_reporting']);
    }

    public static function init()
    {
        // Run configuration
        Errors::config();

        //ob_start();

        // Handlers
        if (self::$handler === 'Startie') {
            set_error_handler("Startie\Errors::errorHandler"); // errors
            set_exception_handler("Startie\Errors::exceptionHandler"); // exceptions
            register_shutdown_function("Startie\Errors::shutdownFunction"); // fatal errors
        }
    }

    public static function errorHandler($level, $message, $file = '', $line = 0)
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Method to call when an uncaught exception occurs
     *
     * @param  mixed $e
     * @return void
     */
    public static function exceptionHandler($e)
    {
        /* Log in storage */

        if (self::$logs) {
            $LogId = self::log($e);
        }

        /* Collect errors */

        if (self::$handler === 'Startie') {
            if (ini_get('display_errors')) {
                if (isset($LogId)) {
                    $errorHTML = self::render($e, "Log generated: #$LogId.");
                } else {
                    $errorHTML = self::render($e, "No log was generated.");
                }
            } else {
                $errorHTML = self::render("Unknown error has been occurred");
            }
        }

        /*
            Display
        */

        ob_get_clean();

        if (self::$handler === 'Startie') {
            echo $errorHTML;
        }
    }

    public static function shutdownFunction()
    {
        // If there was an error and it was fatal
        if ($error = error_get_last() and $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {

            // Clean buffer & don't show default error message
            if (ob_get_length() > 0) {
                ob_end_clean();
            }

            // Show error message for production
            if ($_ENV["MODE_DEV"] == 0) {
                $msg = "Please send out support url of this page";
                self::render($msg);
            }

            // Show error message in dev mode
            elseif ($_ENV['MODE_DEV'] == 1) {
                $msg = "<br>" . $error['message'] . " " . $error['file'] . " [" . $error['line'] . "]";
                self::render($msg);
            }

            // Show only error ID
            else {
                self::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
                self::render("#unknown id error");
            }
        } else {
            // Send & turn off the buffer
            if (ob_get_length() > 0) {
                ob_end_flush();
            }
        }
    }

    public static function log($e)
    {
        // UserId
        $UserIdCurrent = Auth::getIdInService('app') ? Auth::getIdInService('app') : 0;

        // line
        $line = $e->getLine();

        // message
        $message = $e->getMessage();
        $message = substr($message, 0, 65535);
        $message = ($message) ? $message : "";

        // file
        $file = $e->getFile();
        $file = ($file) ? $file : "";

        // url

        $url = $_SERVER['PROTOCOL'] . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'];
        if (isset($_SERVER['QUERY_STRING'])) {
            $url .= "?" . $_SERVER['QUERY_STRING'];
        }
        $url = ($url) ? $url : "";

        // trace
        $trace = $e->getTrace();
        unset($trace[0]['args'][0]->xdebug_message); // delete html from xdebug
        $trace = json_encode($trace);
        if (strlen($trace) > 65535) {
            $trace = substr($trace, 0, 65535);
        }
        $trace = $trace ? $trace : "";

        // type
        if (method_exists($e, 'getType')) {
            $type = $e->getType();
        } else {
            $type = "error";
        }

        // object
        if (method_exists($e, 'getObject')) {
            $object = $e->getObject();
        } else {
            $object = "php";
        }

        $LogId = Logs::generate(
            compact(
                'UserIdCurrent',
                'line',
                'message',
                'file',
                'url',
                'trace',
                'type',
                'object'
            )
        );

        return $LogId;
    }

    public static function render($message, $addition = "")
    {
        return "        
            <div style='margin: 50px; font-family: Menlo, Courier New;'>
                <h1>Error</h1>
                <div style='white-space: pre-wrap; font-size: 15px;'>{$message}<div>
                <div class='margin-top:30px'>{$addition}</div>
            </div>
        ";
    }
}
