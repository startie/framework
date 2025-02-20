<?php

namespace Startie;

class Session
{
    public static function init(): void
    {
        session_start();
    }

    public static function has(string $var): bool
    {
        if (isset($_SESSION[$var])) {
            if ($_SESSION[$var] != "") {
                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated
     */
    public static function is(string $var): bool
    {
        return self::has($var);
    }

    public static function get(string $var = "", string $type = "raw"): mixed
    {
        if ($var !== "") {
            if (Session::has($var)) {
                return Input::session($var, $type);
            } else {
                throw new \Exception(
                    "Unable to get session's item '$var', it doesn't exists"
                );
            }
        }

        return $_SESSION;
    }

    public static function set(string|int $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function push(string|int $key, mixed $value): void
    {
        $_SESSION[$key][] = $value;
    }

    public static function delete(string|int $var): void
    {
        unset($_SESSION[$var]);
    }

    public static function dump(): void
    {
        dump($_SESSION);
    }

    public static function d(): void
    {
        dd($_SESSION);
    }

    public static function dd(): void
    {
        dd($_SESSION);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * @deprecated Use `::dump()` or `::d()`
     */
    public static function view(): void
    {
        dump($_SESSION);
    }
}