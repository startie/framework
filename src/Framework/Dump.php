<?php

namespace Startie;

use Startie\DumpStyle;

class Dump
{
    use \Startie\Bootable;

    public static bool $hasAccess;

    public static function boot(callable $callback = null): void
    {
        self::$isBooted = true;

        // Load access property
        if ($callback) {
            self::$hasAccess = $callback();
        } else {
            self::$hasAccess = true;
        }
    }

    public static function hasAccess(): bool
    {
        self::requireBoot();
        return self::$hasAccess;
    }

    public static function e(mixed $var): void
    {
        self::requireBoot();
        echo "<pre>";
        echo $var;
        echo "</pre>";
    }

    /**
     * For browser network debugging and PHP console
     */
    public static function __pre(mixed $var, string $message = "")
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
    public static function __make(
        mixed $result,
        int $die = 0,
        string $message = "",
        int $trace = 0
    ) {
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
    public static function __made(
        mixed $result,
        string $message = "",
        int $trace = 0
    ): void {
        self::requireBoot();
        Dump::__make($result, 1, $msg, $trace);
        die();
    }

    /**
     * For a simple debugging
     */
    public static function _pre(
        mixed $var,
        string $message = ""
    ): void {
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
    public static function _make(
        mixed $result,
        int $die = 0,
        string $msg = "",
        int $trace = 0
    ): void {
        $debug_backtrace = debug_backtrace();

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
    public static function _made(
        mixed $result,
        string $msg = "",
        int $trace = 0
    ): void {
        self::requireBoot();
        Dump::_make($result, 1, $msg, $trace);
        die();
    }

    /**
     * For a complex debugging
     */
    public static function pre(
        mixed $var,
        string|null $message = null
    ): void     {
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
    public static function make(
        mixed $result,
        int|bool $die = false,
        string $message = "",
        bool|int|null $trace = false
    ): void {
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
    public static function made(
        mixed $result,
        string|null $message = null,
        int|null $trace = 0
    ): void {
        self::requireBoot();
        Dump::make($result, 1, $msg, $trace);
        die();
    }

    /**
     * Start debugging with the openning for <pre>
     */
    public static function start(mixed $var): void
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
    public static function next(mixed $var): void
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
    public static function end(mixed $var): void
    {
        self::requireBoot();
        if (self::hasAccess()) {
            DumpStyle::var_dump($var);
            echo "</pre>";
        }
    }
}