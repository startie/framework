<?php

namespace Startie;

use Startie\Config;
use Startie\App;

class View
{
	/**
	 * Boot is not required
	 */
	use \Startie\Bootable;

	public static function boot()
	{
		self::loadConfig();
		self::$isBooted = true;
	}

	public static function loadConfig()
	{
		try {
			self::$config = Config::get('View');
		} catch (\Exception $e) {
			// Use default config
			self::$config = [
				'trimSpaces' => false
			];
		}
	}

	public static function return(
		$name,
		array $data = [],
		bool|null $trimSpaces = NULL
	) {
		$trimSpaces = $trimSpaces ?? self::$config['trimSpaces'] ?? false;

		$path = App::path("backend/Views/{$name}.php");

		if (!is_file($path)) {
			throw new \Exception("Can't find a view file: '{$path}'\n\n");
		}

		ob_start();
		global $t;

		extract($data);
		require($path);

		$content = ob_get_contents();
		$content = $trimSpaces ? self::trim($content) : $content;

		ob_end_clean();

		return $content;
	}

	public static function r(
		$name,
		array $data = [],
		bool|null $trimSpaces = NULL
	) {
		return self::return($name, $data, $trimSpaces);
	}

	/*
		Fix spaces
		Experimental, not tested well, there is a risk of spoiling view
	*/
	public static function trim($content)
	{
		// d($content);
		$content = str_replace("\n", "", $content);
		$content = str_replace("\t", "", $content);

		// Delete spaces between the tags
		$content = preg_replace("/([a-z]>)( +)(<[a-z])/m", "$1$3", $content);

		// dd($content);

		return $content;
	}

	/**
	 * @deprecated Use `view()` helper
	 */
	public static function render($name, array $data = [])
	{
		$path = App::path("backend/Views/{$name}.php");
		global $t;
		if (!isset($data['t'])) {
			$data['t'] = $t;
		} else {
			//$data['t'] = array_merge($data['t'], $t);
		}
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

	public static function titleChange($title)
	{
		echo "<script>document.title = \"$title\";</script>";
	}
}