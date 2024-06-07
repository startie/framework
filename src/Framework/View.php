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

	/**
	 * Default config
	 */
	public static $config = [
		'trimSpaces' => false
	];

	public static function boot()
	{
		self::loadConfig();
		self::$isBooted = true;
	}

	public static function loadConfig()
	{
		try {
			self::$config = Config::get('View');
		} catch(\Exception $e){
			// Do nothing since config is not required
		}
	}

	/**
	 * r - return
	 */
	public static function r($name, array $data = [], bool $trimSpaces = false)
	{
		$path = App::path("backend/Views/{$name}.php");

		if (!is_file($path)) {
			throw new \Exception("Can't find a view file: '{$path}'\n\n");
		}

		ob_start();
		global $t;

		extract($data);
		require($path);

		$content = ob_get_contents();

		/*
			Fix spaces
			Experimental, not tested well, there is a risk of spoiling view
			Requires third paramether as true or config with global setting
		*/

		if ($trimSpaces || self::$config['trimSpaces']) {
			$content = str_replace("\n", "", $content);
			$content = str_replace("\t", "", $content);
			$content = preg_replace("/ {2,}/m", "", $content);
		}

		ob_end_clean();

		return $content;
	}

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