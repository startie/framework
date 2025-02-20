<?php

namespace Startie;

use Startie\Asseter;

class Js
{
	public static function tag(string $uri): string
	{
		return "<script type='text/javascript' src='$uri'></script>";
	}

	public static function uri(string $uri): string
	{
		$tag = self::tag($uri);
		return $tag;
	}

	public static function public(string $name): string
	{
		$uri = Asseter::getRootUrl() . "{$name}.js";
		$tag = self::tag($uri);
		return $tag;
	}

	public static function page(string $name): void
	{
		$nameArr = explode('/', $name);
		$nameNew = "";
		foreach ($nameArr as $nameEntity) {
			$nameNew .= $nameEntity;
		}

		Asseter::loadPageJs($nameNew);
	}

	public static function frontend(string $name): void
	{
		$html = "";
		if ($_ENV['MODE_DEV']) {
			$path = App::$FRONTEND_DIR . "{$name}.js";
			$html .= "<script>";
			$html .= file_get_contents($path);
			$html .= "</script>";
		}

		echo $html;
	}

	public static function node(string $url): void
	{
		if ($_ENV['MODE_DEV']) {
			echo Js::uri(
				App::$NODE_MODULES_URL . $url
			);
		}
	}

	/**
	 * @deprecated 1.0.0 Bad naming, replaced by `public`
	 */
	public static function p(string $name): string
	{
		return self::public($name);
	}

	/**
	 * @deprecated 1.0.0 Hardcoded echo, replaced by `uri`
	 */
	public static function url(string $url): void
	{
		echo self::tag($url);
	}
}