<?php

use Startie\Dump;

function d($result = NULL, $die = 0, $msg = NULL, $trace = NULL)
{
    Dump::make($result, $die, $msg, $trace);
}

function dd($result = NULL, $msg = NULL, $trace = NULL)
{
    Dump::made($result, $msg, $trace);
}

function t(string $str = "", string $fallback = "")
{
    global $t;
    $result = "";

    if ($str === "") {
        $result = "";
    } else {
        $result = $t[$str]
            ?? $t[str_replace(" ", "_", $str)]
            ?? $fallback
            ?? "";
    }

    return $result;
}