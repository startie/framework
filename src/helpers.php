<?php

use Startie\Asseter;
use Startie\Dump;

/**
 * Shortcut for `Dump::make()`
 */
function d($result = null, $die = 0, $msg = null, $trace = null): void
{
    Dump::make($result, $die, $msg, $trace);
}

/**
 * Shortcut for `Dump::make()`
 */
function dump($result = null, $die = 0, $msg = null, $trace = null): void
{
    Dump::make($result, $die, $msg, $trace);
}

/**
 * Shortcut for `Dump::made()`
 */
function dd($result = null, $msg = null, $trace = null): void
{
    Dump::made($result, $msg, $trace);
}

/**
 * Shortcut for `Texts::translate()`
 */
function t(string $str = "", string $fallback = ""): string
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
 * Shortcut for `View::r()`
 */
function v($name, array $data = [], bool $trimSpaces = false): string
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
 * Shortcut for `Url::controller()`
 */
function url(
    string $routeExpression,
    $controllerParams = null,
    $queryParams = null,
    $arraishQueryParams = false
): string {
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
function template($templatePath, $data, $csrf = null): string
{
    return \Startie\Template::return($templatePath, $data, $csrf);
}

/**
 * Shorcut for `Js::uri()` and `Js::public()`
 */
function js(string $path): string
{
    if (Asseter::isExternal($path)) {
        return \Startie\Js::uri($path);
    } else {
        return \Startie\Js::public($path);
    }
}

/**
 * Shorcut for `Css::uri()` and `Css::public()`
 */
function css(string $path): string
{
    if (Asseter::isExternal($path)) {
        return \Startie\Css::uri($path);
    } else {
        return \Startie\Css::public($path);
    }
}