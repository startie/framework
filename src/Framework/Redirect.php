<?php

namespace Startie;

use Startie\Output;

class Redirect
{
    public static function to(string $url): void
    {
        header('Location: ' . $url);
        die();
    }

    /**
     * Redirect to a specified page's path
     * 
     * Example:
     * 
     * ```php
     * Redirect::page('posts/add');
     * ```
     */
    public static function page(
        string $pagePath,
        string|null $queryString = null
    ): void {
        if (empty($pagePath)) {
            Redirect::to(App::$URL_APP);
        }

        $queryString ??= "";
        if ($queryString !== "") {
            Redirect::to(
                App::$URL_APP . $pagePath . $queryString
            );
        } else {
            Redirect::to(App::$URL_APP . $pagePath);
        }
    }

    /**
     * @param string|null $alternativeUrl Alternative URL, if `urlBeforeLogin` is not set in session
     */
    public static function beforeLogin(
        string|null $alternativeUrl = null
    ): void {
        if (Session::has('urlBeforeLogin')) {
            Redirect::to(Session::get('urlBeforeLogin'));
        } else {
            if (!empty($alternativeUrl)) {
                Redirect::page($alternativeUrl);
            } else {
                if (isset($_ENV['REDIRECT_DEFAULT_URL'])) {
                    Redirect::page($_ENV['REDIRECT_DEFAULT_URL']);
                } else {
                    Redirect::page("");
                }
            }
        }
    }

    public static function referer(string|null $redirectUrl = null): void
    {
        if (!$RedirectUrl) {
            $RedirectUrl = Redirect::getReferer();
        }

        Redirect::to($RedirectUrl);
    }

    public static function getReferer(): string
    {
        return $_SERVER["HTTP_REFERER"];
    }

    /**
     * @deprecated Use `Output::error()`
     */
    public static function e404(): void
    {
        Output::error(404);
    }
}