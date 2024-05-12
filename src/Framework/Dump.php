<?php

namespace Startie;

use Startie\DumpStyle;

class Dump
{
    use \Startie\Bootable;

    public static $hasAccess;

    public static function boot($callback = NULL)
    {
        self::$isBooted = true;

        // Load access property
        if ($callback) {
            self::$hasAccess = $callback();
        } else {
            self::$hasAccess = true;
        }
    }

    public static function hasAccess()
    {
        self::requireBoot();
        return self::$hasAccess;
    }

    public static function e($var)
    {
        self::requireBoot();
        echo "<pre>";
        echo $var;
        echo "</pre>";
    }

    /**
     * For browser network debugging and PHP console
     */
    public static function __pre($var, $msg = "")
    {
        self::requireBoot();
        if (self::$hasAccess) {
            var_dump($var);
            echo $msg . "\n";
        }
    }

    /**
     * For browser network debugging and PHP console
     */
    public static function __make($result, $die = 0, $msg = "", $trace = 0)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            Dump::__pre($result, $msg);
            if ($trace) {
                Dump::pre(debug_backtrace());
            }
            if ($die) die();
        }
    }

    /**
     * For browser network debugging and PHP console
     */
    public static function __made($result, $msg = "", $trace = 0)
    {
        self::requireBoot();
        Dump::__make($result, 1, $msg, $trace);
        die();
    }

    /**
     * For a simple debugging
     */
    public static function _pre($var, $msg = "")
    {
        self::requireBoot();
        if (self::hasAccess()) {
            echo "<pre>";
            echo $msg . "\n";
            var_dump($var);
            echo "</pre>";
        }
    }

    /**
     * For a simple debugging
     */
    public static function _make($result, $die = 0, $msg = "", $trace = 0)
    {
        self::requireBoot();
        $backTrace = "";
        $backTrace .= "<mark>";
        $backTrace .= "[" . debug_backtrace()[1]['line'] . "]";
        $backTrace .= " " . debug_backtrace()[1]['file'];
        $backTrace .= "</mark>";
        $backTrace .= "<br>";

        if (self::hasAccess()) {
            Dump::_pre($result, $msg . $backTrace);
            if ($trace) {
                Dump::pre(debug_backtrace());
            }
            if ($die) die();
        }
    }

    /**
     * For a simple debugging
     */
    public static function _made($result, $msg = "", $trace = 0)
    {
        self::requireBoot();
        Dump::_make($result, 1, $msg, $trace);
        die();
    }

    /**
     * For a complex debugging
     */
    public static function pre($var, $msg = "")
    {
        self::requireBoot();
        if (self::hasAccess()) {
            echo "<pre>";
            echo $msg . "\n";
            DumpStyle::var_dump($var);
            echo "</pre>";
        }
    }

    /**
     * For a complex debugging
     * 
     * TODO: remove vscode openning hardcode
     */
    public static function make($result, $die = 0, $msg = "", $trace = 0)
    {
        self::requireBoot();
        $backTrace = "";

        $linkStyles = "font-size: 15px; line-height: 22px";
        $backTraceArr = debug_backtrace();
        $backTraceArr = array_reverse($backTraceArr);
        for ($i = 0; $i < count($backTraceArr); $i++) {
            if (
                strpos(
                    $backTraceArr[$i]['file'] ?? "",
                    "Framework/Core/Dump"
                ) === false
            ) {
                $line = $backTraceArr[$i]['line'] ?? 0;
                $file = $backTraceArr[$i]['file'] ?? "";
                if ($file !== "") {
                    $path = "";
                    $path .= "{$file}";
                    $path .= ":$line";
                    $url = "vscode://file$path";

                    $backTrace .= "<a style='$linkStyles' href='$url'>";
                    $backTrace .= $path;
                    $backTrace .= "</a>";
                    $backTrace .= "\n";
                }
            }
        }

        if (self::hasAccess()) {
            Dump::pre($result, $msg . $backTrace);
            if ($trace) {
                Dump::pre(debug_backtrace());
            }
            if ($die) die();
        }
    }

    /**
     * For a complex debugging
     */
    public static function made($result, $msg = "", $trace = 0)
    {
        self::requireBoot();
        Dump::make($result, 1, $msg, $trace);
        die();
    }

    /**
     * Start debugging with the openning for <pre>
     */
    public static function start($var)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            echo "<pre>";
            DumpStyle::var_dump($var);
            echo "<br>";
        }
    }

    /**
     * Continue debugging
     */
    public static function next($var)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            DumpStyle::var_dump($var);
            echo "<br>";
        }
    }

    /**
     * End debugging
     */
    public static function end($var)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            DumpStyle::var_dump($var);
            echo "</pre>";
        }
    }
}