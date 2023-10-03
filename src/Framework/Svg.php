<?php

namespace Startie;

class Svg
{
	public static function pdir($path)
	{
		$filePath = PUBLIC_DIR . $path . '.svg';
		$file = file_get_contents($filePath);
		return $file;
	}

	public static function p($url)
	{
		$fileUrl = PUBLIC_DIR . $url . '.svg';

		#1

		if (file_exists($fileUrl)) {
			$file = file_get_contents($fileUrl);
			echo $file;
		} else {
			echo "no file on $fileUrl";
		}
	}

	public static function html($url)
	{
		$fileUrl = PUBLIC_URL . $url . '.svg';

		echo "<image src='{$fileUrl}'>";
	}
}