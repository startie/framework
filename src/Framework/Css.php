<?php

declare(strict_types=1);

namespace Startie;

use Startie\Asseter;

class Css
{
	public static function tag(string $uri): string
	{
		return "<link rel='stylesheet' type='text/css' href='$uri'>";
	}

	public static function uri(string $uri): string
	{
		$tag = self::tag($uri);
		return $tag;
	}

	public static function public(string $name): string
	{
		$uri = Asseter::getRootUrl() . "{$name}.css";
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

		Asseter::loadPageCss($nameNew);
	}

	public static function frontend(string $name): void
	{
		$html = "";
		if ($_ENV['MODE_DEV']) {
			$path = App::$FRONTEND_DIR . "$name.css";
			$html .= "<style>";
			$html .= file_get_contents($path);
			$html .= "</style>";
		}

		echo $html;
	}

	/**
	 * @deprecated 0.46.0
	 */
	public static function node(string $uri): void
	{
		if ($_ENV['MODE_DEV']) {
			echo self::uri(App::$NODE_MODULES_URL . $uri);
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
	public static function url(string $uri): void
	{
		echo self::tag($uri);
	}
}