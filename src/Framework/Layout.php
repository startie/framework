<?php

namespace Startie;

class Layout
{
	public static function return($name, array $data = [])
	{
		if (!$name) {
			return "";
		}

		$path = App::path("backend/Layouts/{$name}.php");

		if (is_file($path)) {
			ob_start();
			extract($data);
			require($path);
			$content = ob_get_contents();
			ob_end_clean();
		} else {
			throw new \Startie\Exception("Can't find a view file: '{$path}'\n\n");
		}

		return $content;
	}

	public static function block($name)
	{
		return "{{{$name}}}";
	}
}