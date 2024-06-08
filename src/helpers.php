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
    return \Startie\Texts::translate($str, $fallback);
}

function view($name, array $data = [], bool $trimSpaces = false)
{
    return \Startie\View::r($name, $data, $trimSpaces);
}

function v($name, array $data = [], bool $trimSpaces = false)
{
    return \Startie\View::r($name, $data, $trimSpaces);
}

function url(
    string $routeExpression,
    $controllerParams = NULL,
    $queryParams = NULL,
    $arraishQueryParams = false
) {
    return \Startie\Url::controller(
        $routeExpression,
        $controllerParams,
        $queryParams,
        $arraishQueryParams
    );
}

function template($templatePath, $data, $csrf = NULL)
{
    \Startie\Template::return($templatePath, $data, $csrf);
}