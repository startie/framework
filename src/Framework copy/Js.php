<?php

namespace Startie;

class Js
{
	public static function url($url)
	{
		echo '<script src="' . $url . '"></script>';
	}

	public static function node($url)
	{
		die('deprecated');
		// if ($_ENV['MODE_DEV']) {
		// 	Js::url(NODE_MODULES_URL . $url);
		// }
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

	public static function p($name)
	{
		$path = PUBLIC_URL . $name . ".js";
		echo "<script src='$path'></script>";
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
}
