<?php

namespace Startie;

class Cookie
{
    public static function is($var)
    {
        return isset($_COOKIE[$var]);
    }

    public static function get($var, $type = "raw")
    {
        if (Cookie::is($var)) {
            return Input::cookie($var, $type);
        }
        return false;
    }

    public static function set($key, $value, $minutes = 0, $domain = "")
    {
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

    public static function delete($key)
    {
        setcookie($key, "", time() - 3600, "/");
    }
}