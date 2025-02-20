<?php

namespace Startie;

class Procedure
{
	public static function inc(
		string $name,
array $data = []
	): void 	{
		extract($data);
		$path = App::path("backend/Procedures/{$name}.php");
		global $t;
		require($path);
	}

	public static function return(
		string $name,
array $data = []
	): string 	{
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