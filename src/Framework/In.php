<?php

namespace Startie;

class In
{
	/**
	 * 
	 * Get a variable value from global array and make optional modifications.
	 * 
	 * ```php
	 * In::e('get', 'query', 'STR', ['', NULL], ['trim']);
	 * ```
	 * 
	 * @param  string $global 			A name of global array: 'get' for $_GET, 'post' for $_POST, etc.
	 * @param  string $name 			A variable name.
	 * @param  string $SanitizeType 	Type string for sanitizing.
	 * @param  array $if 				Array of 3 values: 0 – value for equality check; 1 – true case substitute; 2 – false case substitute
	 * @param  array $processing		Array of functions to call on value.
	 * @param  array $replacements		Array of replacements.
	 * 
	 * @return mixed
	 * 
	 */

	public static function e($global, $name, $SanitizeType, $if = [], $processing = [], $replacements = [])
	{
		/*
			fix name
		*/

		/*
			evaluate type
		*/

		if ($SanitizeType === '') {
			$SanitizeType = Input::$SanitizeTypeDefault;
		}

		$data = Input::$global($name, $SanitizeType);

		/* if */

		if (!empty($if)) {
			if ($data === $if[0]) {
				$data = $if[1];
			} else {
				if (!empty($if[2])) {
					$data = $if[2];
				} else {
					//$data = $data;
				}
			}
		}

		/* processing */

		if (!empty($processing) && $data) {
			foreach ($processing as $f) {
				$data = call_user_func($f, $data);
			}
		}

		/* replacements */

		if (!empty($replacements)) {
			foreach ($replacements as $r) {
				preg_replace($r[0], $r[1], $data);
			}
		}

		return $data;
	}
	public static function cookie($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("cookie", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function env($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("env", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function files($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("files", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function get($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("get", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function post($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("post", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function request($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("request", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function server($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("server", $name, $SanitizeType, $if, $processing, $replacements);
	}

	public static function session($name, $SanitizeType = '', $if = [], $processing = [], $replacements = [])
	{
		return In::e("session", $name, $SanitizeType, $if, $processing, $replacements);
	}
}
