<?php

namespace Startie;

class Session
{
    public static function init()
    {
        session_start();
    }

    public static function dump()
    {
        dd($_SESSION);
    }

    public static function is($var)
    {
        if (isset($_SESSION[$var])) {
            if ($_SESSION[$var] != "") {
                return true;
            }
        }
    }

    public static function get($var = "", $type = "raw")
    {
        if ($var !== "") {
            if (Session::is($var)) {
                return Input::session($var, $type);
            } else {
                throw new \Exception(
                    "Unable to get session's item '$var', it doesn't exists"
                );
            }
        }
        return $_SESSION;
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function push($key, $value)
    {
        $_SESSION[$key][] = $value;
    }

    public static function delete($var)
    {
        unset($_SESSION[$var]);
    }

    public static function view()
    {
        dump($_SESSION);
    }

    public static function destroy()
    {
        $_SESSION = array();
        session_destroy();
    }
}