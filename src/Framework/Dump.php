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

        # Load property

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

    #
    #   1. 
    #   a) For JS network console
    #   b) For PHP console
    #

    public static function __pre($var, $msg = "")
    {
        self::requireBoot();
        if (self::$hasAccess) {
            var_dump($var);
            echo $msg . "\n";
        }
    }

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

    public static function __made($result, $msg = "", $trace = 0)
    {
        self::requireBoot();
        Dump::__make($result, 1, $msg, $trace);
        die();
    }

    #
    #   For simple debug
    #

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

    public static function _made($result, $msg = "", $trace = 0)
    {
        self::requireBoot();
        Dump::_make($result, 1, $msg, $trace);
        die();
    }

    #
    #   For complex debug
    #

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

    public static function make($result, $die = 0, $msg = "", $trace = 0)
    {
        self::requireBoot();
        $backTrace = "";
        $backTrace .= "<mark>";

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
                $backTrace .= "[$line]";
                $backTrace .= "\t{$file}\n";
            }
        }

        $backTrace .= "</mark>";

        if (self::hasAccess()) {
            Dump::pre($result, $msg . $backTrace);
            if ($trace) {
                Dump::pre(debug_backtrace());
            }
            if ($die) die();
        }
    }

    public static function made($result, $msg = "", $trace = 0)
    {
        self::requireBoot();
        Dump::make($result, 1, $msg, $trace);
        die();
    }

    public static function start($var)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            echo "<pre>";
            DumpStyle::var_dump($var);
            echo "<br>";
        }
    }

    public static function next($var)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            DumpStyle::var_dump($var);
            echo "<br>";
        }
    }

    public static function end($var)
    {
        self::requireBoot();
        if (self::hasAccess()) {
            DumpStyle::var_dump($var);
            echo "</pre>";
        }
    }
}
