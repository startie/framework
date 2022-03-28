<?php

namespace Startie;

class Errors
{
    public static function init()
    {
        if (Access::is('developers') || Access::is('admins')) {
            # Report all PHP errors
            error_reporting(E_ALL);

            # Works good sometimes, maybe uncomment?
            // ini_set('display_errors', 1); 
            // error_reporting(~0);
        } else {
            //error_reporting(0);
            error_reporting(E_ERROR | E_PARSE);
        }

        # Do not log repeated messages
        ini_set('ignore_repeated_errors', 1);

        // # Start remembering everything that would normally be outputted, but don't quite do anything with it yet.
        ob_start();

        set_error_handler("Startie\Errors::errorHandler");
        register_shutdown_function("Startie\Errors::shutdownFunction");
    }

    #
    #   @param int $errno уровень ошибки
    #   @param string $errstr сообщение об ошибке
    #   @param string $errfile имя файла, в котором произошла ошибка
    #   @param int $errline номер строки, в которой произошла ошибка
    #   @return boolean
    #

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() & $errno) {
            $errors = array(
                E_ERROR => 'E_ERROR',
                E_WARNING => 'E_WARNING',
                E_PARSE => 'E_PARSE',
                E_NOTICE => 'E_NOTICE',
                E_CORE_ERROR => 'E_CORE_ERROR',
                E_CORE_WARNING => 'E_CORE_WARNING',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                E_USER_ERROR => 'E_USER_ERROR',
                E_USER_WARNING => 'E_USER_WARNING',
                E_USER_NOTICE => 'E_USER_NOTICE',
                E_STRICT => 'E_STRICT',
                E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                E_DEPRECATED => 'E_DEPRECATED',
                E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            );

            # Make own error message
            $errurl = $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];

            if (!is_int($errline)) {
                $errline = 0;
            };

            $CurrentUserId = Auth::getIdInService('app');
            $CurrentUserId = ($CurrentUserId) ? $CurrentUserId : 0;

            $appLogId = AppLogs::create([
                'insert' => [
                    ['createdAt', '`UTC_TIMESTAMP()`'],
                    ['UserId', $CurrentUserId, 'INT'],
                    ['line', $errline, 'INT'],
                    ['file', $errfile, 'STR'],
                    ['url', $errurl, 'STR'],
                    ['message', $errors[$errno] . ': ' . $errstr, 'STR'],
                    ['type', 'errors', 'STR'],
                    ['object', 'php', 'STR'],
                ]
            ]);

            return $appLogId;
        }

        # Do not run internal PHP errors handler
        return true;
    }

    public static function shutdownFunction()
    {
        # If there was an error and it was fatal
        if ($error = error_get_last() and $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            # Clean buffer & don't show default error message
            ob_end_clean();

            # Forming error message
            if (Config::$stage == 'PRODUCTION' && $_ENV['MODE_DEV'] == 0) {
                $msg = "Send your administrator url of this page ";
            } else {
                $msg = "<br> " . $error['message'] . " " . $error['file'] . " [" . $error['line'] . "]";
            }

            # Show error message
            if ($_ENV['MODE_DEV']) {
                echo "
                <div style='margin: 50px'>
                    <h3 style='font-family: Arial;'>Error</h3>
                    <div style='font-family: Courier New; white-space: pre-wrap; font-size: 15px;'>$msg<div>
                </div>";
            } else {
                $appLogId = error_handler($error['type'], $error['message'], $error['file'], $error['line']);
                if ($appLogId) {
                    echo "<div style='text-align: center; font-family: Arial; position: absolute;'><h3>Error</h3> #" . $appLogId . "</div>";
                } else {
                    echo "<div style='text-align: center; font-family: Arial; position: absolute;'><h3>Error</h3> Undefined</div>";
                }
            }
        } else {
            # Send & turn off the buffer
            ob_end_flush();
        }
    }
}
