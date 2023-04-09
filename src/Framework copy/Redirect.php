<?php

namespace Startie;

class Redirect
{
	#
	#
	#	Vars:
	#	$url – str
	#
	#

	public static function to($url)
	{
		header('Location: ' . $url);
		die();
	}

	#
	#
	#	Еxample:
	#	Redirect::page('posts/add')	
	#
	#	Vars:
	#	$pageName – str
	#	$q – query string
	#
	#

	public static function page($pageName, $q = NULL)
	{
		if (empty($pageName)) {
			Redirect::to(URL_APP);
		}

		if (!empty($q)) {
			Redirect::to(URL_APP . $pageName . $q);
		} else {
			Redirect::to(URL_APP . $pageName);
		}
	}

	#
	#	
	#	Vars:
	#	$alt – alternative url, if $urlBeforeLogin is not set
	#
	#

	public static function beforeLogin($alt = NULL)
	{
		if (Session::is('urlBeforeLogin')) {
			Redirect::to(Session::get('urlBeforeLogin'));
		} else {
			if (!empty($alt)) {
				Redirect::page($alt);
			} else {
				if (isset($_ENV['REDIRECT_DEFAULT_URL'])) {
					Redirect::page($_ENV['REDIRECT_DEFAULT_URL']);
				} else {
					Redirect::page("");
				}
			}
		}
	}

	#
	#	Todo:
	#	- добавить уровни, помнить историю с определенным количеством ячеек
	#

	public static function referer($RedirectUrl = NULL)
	{
		if (!$RedirectUrl) {
			$RedirectUrl = Redirect::getReferer();
		}

		Redirect::to($RedirectUrl);
	}

	public static function getReferer()
	{
		return $_SERVER["HTTP_REFERER"];
	}

	public static function e404()
	{
		header("HTTP/1.0 404 Not Found");
	}
}
