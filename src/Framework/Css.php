<?php

namespace Startie;

class Css
{
	public static function url($url)
	{
		echo '<link href="' . $url . '" rel="stylesheet" type="text/css">';
	}

	public static function node($url)
	{
		if ($_ENV['MODE_DEV']) {
			Css::url(NODE_MODULES_URL . $url);
		}
	}

	public static function page($name)
	{
		$nameNew = "";
		$nameArr = explode('/', $name);
		foreach ($nameArr as $nameEntity) {
			$nameNew .= $nameEntity;
		}
		Asseter::loadPageCss($nameNew);
	}

	public static function p($name)
	{
		$path = PUBLIC_URL . $name . ".css";
		echo "<link rel='stylesheet' href='$path'>";
	}

	public static function frontend($name)
	{
		if ($_ENV['MODE_DEV']) {
			$path = FRONTEND_DIR . $name . ".css";
			echo "<style>";
			echo file_get_contents($path);
			echo "</style>";
		}
	}
}
