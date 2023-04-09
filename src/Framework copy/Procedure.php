<?php

namespace Startie;

class Procedure
{
	public static function inc($name, array $data = [])
	{
		extract($data);
		$path = App::path("backend/Procedures/{$name}.php");
		require($path);
	}

	public static function return($name, array $data = [])
	{
		$path = App::path("backend/Procedures/{$name}.php");

		if (is_file($path)) {
			ob_start();
			extract($data);
			require($path);
			$content = ob_get_contents();

			// Dangerous because of spoiling data displaying
			// $content = str_replace("\n", "", $content);
			// $content = str_replace("\t", "", $content);
			// $content = preg_replace("/ {2,}/m", "", $content);

			ob_end_clean();
		} else {
			throw new Exception(sprintf('Cant find procedure file %s!', $path));
		}

		return $content;
	}
}
