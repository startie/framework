<?php

namespace Startie;

use Startie\Asseter;

class Js
{
	public static function url(string $url): void
	{
		echo '<script src="' . $url . '"></script>';
	}

	public static function public(string $name): string
	{
		$path = PUBLIC_URL . $name . ".js";
		return "<script src='$path'></script>";
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
			$path = FRONTEND_DIR . $name . ".js";
			$html .= "<script>";
			$html .= file_get_contents($path);
			$html .= "</script>";
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
			Js::url(NODE_MODULES_URL . $url);
		}
	}
}
