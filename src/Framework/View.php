<?php

namespace Startie;

class View
{
	public static function r($name, array $data = [])
	{
		$path = App::path("backend/Views/{$name}.php");

		if (is_file($path)) {
			ob_start();
			global $t;

			extract($data);
			require($path);

			$content = ob_get_contents();

			/*
				Fix spaces
				Dangerous because of spoiling data displaying
			*/

			// $content = str_replace("\n", "", $content);
			// $content = str_replace("\t", "", $content);
			// $content = preg_replace("/ {2,}/m", "", $content);

			ob_end_clean();
		} else {
			throw new Exception("Can't find a view file: '{$path}'\n\n");
		}

		return $content;
	}

	public static function render($name, array $data = [])
	{
		$path = App::path("backend/Views/{$name}.php");
		global $t;
		extract($data);
		require($path);
	}

	public static function utils()
	{
		function v($a, $b = [])
		{
			return View::r($a, $b);
		}
	}
}
