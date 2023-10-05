<?php

namespace Startie;

use Startie\Asseter;

class Css
{
	public static function url(string $url): void
	{
		echo '<link href="' . $url . '" rel="stylesheet" type="text/css">';
	}

	public static function public(string $name): string
	{
		$path = PUBLIC_URL . $name . ".css";
		return "<link rel='stylesheet' href='$path'>";
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
			$path = FRONTEND_DIR . $name . ".css";
			$html .= "<style>";
			$html .= file_get_contents($path);
			$html .= "</style>";
		}

		echo $html;
	}

	/**
	 * @deprecated
	 */
	public static function p(string $name): string
	{
		return self::public($name);
	}

	/**
	 * @deprecated
	 */
	public static function node(string $url): void
	{
		if ($_ENV['MODE_DEV']) {
			Css::url(NODE_MODULES_URL . $url);
		}
	}
}
