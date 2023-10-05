<?php

namespace Startie;

use Startie\Asseter;

class Js
{
	public static function url($url)
	{
		echo '<script src="' . $url . '"></script>';
	}

	public static function public($name)
	{
		$path = PUBLIC_URL . $name . ".js";
		echo "<script src='$path'></script>";
	}

	public static function p($name)
	{
		self::public($name);
	}

	public static function page($name)
	{
		$nameNew = "";
		$nameArr = explode('/', $name);
		foreach ($nameArr as $nameEntity) {
			$nameNew .= $nameEntity;
		}
		Asseter::loadPageJs($nameNew);
	}
	
	public static function frontend($name)
	{
		if ($_ENV['MODE_DEV']) {
			$path = FRONTEND_DIR . $name . ".js";
			echo "<script>";
			echo file_get_contents($path);
			echo "</script>";
		}
	}

	/**
	 * @deprecated
	 */
	public static function node($url)
	{
		if ($_ENV['MODE_DEV']) {
			Js::url(NODE_MODULES_URL . $url);
		}
	}
}
