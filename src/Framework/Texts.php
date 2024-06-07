<?php

namespace Startie;

use Startie\Php;
use Startie\Language;
use Startie\App;

class Texts
{
	use \Startie\Bootable;

	/**
	 * Default config
	 */
	public static $config = [
		'autoFallback' => false,
	];

	public static function loadConfig()
	{
		try {
			$config = Config::get('Texts');
			self::$config = $config;
		} catch (\Exception $e) {
			// Do nothing since config is not required
		}
	}

	/**
	 * Boot is required
	 */
	public static function boot()
	{
		self::loadConfig();

		// Get current language
		$LanguageCode = Language::code();

		// Load texts from `backend/Texts`
		$textsPaths = []; // ['Common/ru', 'Users/ru', ... ]
		$textsPath = App::path("backend/Texts/");
		$textsFiles = scandir($textsPath);
		foreach ($textsFiles as $textsFile) {
			if ($textsFile !== "." && $textsFile !== "..") {
				$textsPaths[] = "{$textsFile}/$LanguageCode";
			}
		}

		// Register the util
		/**
		 * Short for `translate`
		 * 
		 * @deprecated In project delete all namespaced imports, 
		 * and use global helper
		 * 
		 * But don't delete it from source code of a framework
		 * for backward compatability
		 */
		function t(string $target = "", string $fallback = "")
		{
			return \Startie\Texts::translate($target, $fallback);
		}

		// Collect texts
		$t = self::collect($textsPaths);
		$GLOBALS['t'] = $t;

		self::$isBooted = true;
	}

	/**
	 * @deprecated Use `boot()`
	 */
	public static function init()
	{
		self::boot();
	}

	public static function getPhrase($path, $phrase, $params = [])
	{
		$vocab = self::get($path);
		$phrase = $vocab[$phrase];

		if (!empty($params)) {
			$paramsNew = [];
			foreach ($params as $i => $param) {
				$paramsNew['{{' . $i . '}}'] = $param;
			}

			foreach ($paramsNew as $i => &$param) {
				$phrase = str_replace($i, $param, $phrase);
			}
		}

		return $phrase;
	}

	public static function collect($arr)
	{
		$vArr = [];
		foreach ($arr as $v) {
			$vArr[] = self::get("$v");
		}
		return call_user_func_array('array_merge', $vArr);
	}

	public static function get($path, $params = [])
	{
		$v = "";
		$fullPath = App::path("backend/Texts/{$path}.json");

		if (file_exists($fullPath)) {
			$v = json_decode(file_get_contents($fullPath), true);

			if ($v === null && json_last_error() !== JSON_ERROR_NONE) {
				throw new \Startie\Exception(
					"JSON data on $fullPath of Texts incorrect"
				);
			}

			# Arrifying
			if (isset($params['arrify'])) {
				if ($params['arrify'] == 1) {
					$vArrfied = [];
					foreach ($v as $i => $value) {
						$x = $i - 1;
						$vArrfied[$x]['id'] = $i;
						$vArrfied[$x]['name'] = Php::mb_ucfirst($value);
					}
					return $vArrfied;
				}
			}
			# /Arrifying

			return $v;
		} else {

			throw new \Startie\Exception("Texts on $fullPath doesn't exists");
		}
	}

	public static function translate(string $target = "", string $fallback = "")
	{
		if ($fallback === "" && self::$config['autoFallback']) {
			$fallback = $target;
		}

		global $t;
		$result = "";

		if ($target === "") {
			$result = "";
		} else {
			$result = $t[$target]
				?? $t[str_replace(" ", "_", $target)]
				?? $fallback
				?? "";
		}

		return $result;
	}
}