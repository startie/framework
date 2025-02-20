<?php

namespace Startie;

class Svg
{
	public static function pdir(string $path): string
	{
		$filePath = App::$PUBLIC_DIR . $path . '.svg';
		$file = file_get_contents($filePath);

		return $file;
	}

	public static function p(string $url): void
	{
		$fileUrl = App::$PUBLIC_DIR . $url . '.svg';

		if (file_exists($fileUrl)) {
			$file = file_get_contents($fileUrl);
			echo $file;
		} else {
			echo "no file on $fileUrl";
		}
	}

	public static function html(string $url): void
	{
		$fileUrl = App::$PUBLIC_URL . $url . '.svg';

		echo "<image src='{$fileUrl}'>";
	}
}