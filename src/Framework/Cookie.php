<?php

namespace Startie;

class Cookie
{
    public static function is(string $var): bool
    {
        return isset($_COOKIE[$var]);
    }

    /**
     * @return mixed|false Returns cookie value of `false` when it doesn't exist
     */
    public static function get(string $var, string $type = "raw")
    {
        if (Cookie::is($var)) {
            return Input::cookie($var, $type);
        }
        return false;
    }

    public static function set(
        string $key,
        string $value,
        int $minutes = 0,
        string $domain = ""
    ): void {
        if (!$domain) {
            $domain = '/';
        };

        if ($minutes) {
            $minutes = time() + $minutes * 60;
            setcookie($key, $value, $minutes, $domain);
        }

        if (!$minutes) {
            setcookie($key, $value, time() + (10 * 365 * 24 * 60 * 60), $domain);
        }
    }

    public static function delete(string $key): void
    {
        setcookie($key, "", time() - 3600, "/");
    }
}