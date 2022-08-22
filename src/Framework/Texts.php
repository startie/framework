<?php

namespace Startie;

use Startie\Vocabulary;
use Startie\Php;
use Startie\Language;
use Startie\App;

class Texts
{
	public static function init()
	{
		$LanguageCode = Language::code();

		$ConfigTexts = require App::path('backend/Config/Texts/Common.php');

		$ConfigTextsPaths = array_map(
			function ($name) use ($LanguageCode) {
				return $name . "/$LanguageCode";
			},
			$ConfigTexts
		);

		/* Register utils */

		function t($str = "")
		{
			global $t;
			if ($str == "") {
				return "";
			} else {
				return $t[$str] ?? $t[str_replace(" ", "_", $str)];
			}
		}

		$t = self::collect($ConfigTextsPaths);

		$GLOBALS['t'] = $t;
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
				throw new \Startie\Exception("JSON data on $fullPath of Texts incorrect");
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
}
