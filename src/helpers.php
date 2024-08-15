<?php

use Startie\Dump;

/**
 * Shortcut for `Dump::make()`
 */
function d($result = NULL, $die = 0, $msg = NULL, $trace = NULL)
{
    Dump::make($result, $die, $msg, $trace);
}

/**
 * Shortcut for `Dump::make()`
 */
function dump($result = NULL, $die = 0, $msg = NULL, $trace = NULL)
{
    Dump::make($result, $die, $msg, $trace);
}

/**
 * Shortcut for `Dump::made()`
 */
function dd($result = NULL, $msg = NULL, $trace = NULL)
{
    Dump::made($result, $msg, $trace);
}

/**
 * Shortcut for `Texts::translate()`
 */
function t(string $str = "", string $fallback = "")
{
    return \Startie\Texts::translate($str, $fallback);
}

/**
 * Shortcut for `View::r()`
 */
function view($name, array $data = [], bool $trimSpaces = false)
{
    return \Startie\View::r($name, $data, $trimSpaces);
}

/**
 * Shortcut for `View::setTitle()`
 */
function title(string $title): void
{
    \Startie\View::setTitle($title);
}

/**
 * Shortcut for `View::r()`
 */
function v($name, array $data = [], bool $trimSpaces = false)
{
    return \Startie\View::r($name, $data, $trimSpaces);
}

/**
 * Shortcut for `Url::controller()`
 */
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

/**
 * Shortcut for `Template::return()`
 */
function template($templatePath, $data, $csrf = NULL)
{
    return \Startie\Template::return($templatePath, $data, $csrf);
}