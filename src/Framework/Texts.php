<?php

namespace Startie;

use Startie\Php;
use Startie\Language;
use Startie\App;

class Texts
{
	use \Startie\Bootable;

	public static function loadConfig(): void
	{
		try {
			$config = Config::get('Texts');
			self::$config = $config;
		} catch (\Exception $e) {
			// Use default config
			self::$config = [
				'autoFallback' => false,
			];
		}
	}

	/**
	 * Boot is required
	 */
	public static function boot(): void
	{
		self::loadConfig();

		// Get current language
		$LanguageCode = Language::code();

		// Load texts from `backend/Texts`
		$textsPaths = []; // ['Common/ru', 'Users/ru', ... ]
		$textsPath = App::path("backend/Texts/");
		$textsFiles = scandir($textsPath);
		foreach ($textsFiles as $textsFile) {
			if (
				$textsFile !== "."
				&& $textsFile !== ".."
				&& $textsFile !== ".DS_Store"
			) {
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
		function t(string $target = "", string $fallback = ""): string
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
	public static function init(): void
	{
		self::boot();
	}

	public static function getPhrase(
		string $path,
		string $phrase,
		array $params = []
	): string {
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

	public static function collect(array $arr): mixed
	{
		$vArr = [];
		foreach ($arr as $v) {
			$vArr[] = self::get("$v");
		}
		return call_user_func_array('array_merge', $vArr);
	}

	public static function get(string $path, array $params = []): array
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

	public static function translate(
		string $target = "",
		string $fallback = ""
	): string {
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