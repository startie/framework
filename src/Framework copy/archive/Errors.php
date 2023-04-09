<?php

namespace Startie;

class Erorrs
{
    // /**
    //  * Method to call when an error occurs
    //  *
    //  * @param  mixed $level
    //  * @param  mixed $text
    //  * @param  mixed $file
    //  * @param  mixed $line
    //  * @return void
    //  */
    // public static function errorHandler($level, $text, $file, $line)
    // {
    //     if (error_reporting() & $level) {
    //         $errors = [
    //             E_ERROR => 'E_ERROR',
    //             E_WARNING => 'E_WARNING',
    //             E_PARSE => 'E_PARSE',
    //             E_NOTICE => 'E_NOTICE',
    //             E_CORE_ERROR => 'E_CORE_ERROR',
    //             E_CORE_WARNING => 'E_CORE_WARNING',
    //             E_COMPILE_ERROR => 'E_COMPILE_ERROR',
    //             E_COMPILE_WARNING => 'E_COMPILE_WARNING',
    //             E_USER_ERROR => 'E_USER_ERROR',
    //             E_USER_WARNING => 'E_USER_WARNING',
    //             E_USER_NOTICE => 'E_USER_NOTICE',
    //             E_STRICT => 'E_STRICT',
    //             E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
    //             E_DEPRECATED => 'E_DEPRECATED',
    //             E_USER_DEPRECATED => 'E_USER_DEPRECATED',
    //         ];

    //         $UserIdCurrent = Auth::getIdInService('app') ?? 0;

    //         $line = is_int($line) ? $line : 0;
    //         $message = $errors[$level] . ': ' . $text;
    //         #$file

    //         if (isset($_SERVER['QUERY_STRING'])) {
    //             $url = $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];
    //         } else {
    //             $url = $_SERVER['SCRIPT_NAME'];
    //         }

    //         $type = "error";
    //         $object = "php";

    //         $LogId = Logs::generate(
    //             compact(
    //                 'UserIdCurrent',
    //                 'line',
    //                 'message',
    //                 'file',
    //                 'url',
    //                 'type',
    //                 'object'
    //             )
    //         );

    //         // To not run internal PHP errors handler
    //         return $LogId;
    //     }

    //     // To not run internal PHP errors handler
    //     return true;
    // }

    // public static function errorHandler($level, $message, $file, $line)
    // {
    //     // Handles @ error suppression
    //     if (error_reporting() === 0) {
    //         return false;
    //     }

    //     throw new \Exception($message, 0, $level, $file, $line);
    //     //throw new \Startie\Exception($message, "error", "php", 0, $errfile, $errline);
    // }
}
