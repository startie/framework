<?php

declare(strict_types=1);

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
        if (is_callable($callback)) {
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
     * @psalm-suppress ForbiddenCode
     */
    public static function __pre(mixed $var, string $message = "")
    {
        self::requireBoot();
        if (self::$hasAccess) {
            var_dump($var);
            echo $message . PHP_EOL;
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
            Dump::__pre($result, $message);
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
        Dump::__make($result, 1, $message, $trace);
        die();
    }

    /**
     * For a simple debugging
     * @psalm-suppress ForbiddenCode
     */
    public static function _pre(
        mixed $var,
        string $message = ""
    ): void {
        self::requireBoot();
        if (self::hasAccess()) {
            echo "<pre>";
            echo $message . "\n";
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
        $backTrace .= "[" . ($debug_backtrace[1]['line'] ?? "") . "]";
        $backTrace .= " " . ($debug_backtrace[1]['file'] ?? "");
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
    ): void {
        self::requireBoot();

        $message ??= "";

        if (self::hasAccess()) {
            echo "<pre>";
            echo $message . "\n";
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
        bool $die = false,
        string $message = "",
        bool $trace = false
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
            Dump::pre($result, $message . $backTrace);
            if ($trace) {
                Dump::pre(debug_backtrace());
            }
            if ($die) {
                die();
            }
        }
    }

    /**
     * For a complex debugging
     */
    public static function made(
        mixed $result,
        string $message = "",
        bool $trace = false
    ): void {
        self::requireBoot();
        Dump::make($result, true, $message, $trace);
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