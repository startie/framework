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
	 * @param  string $global 			A name of global array: $_GET, $_POST
	 * @param  string $name 			A variable name.
	 * @param  string $type 			Type for sanitizing.
	 * @param  array $if 				Array of 3 values: 0 – value for equality check; 1 – true case substitute; 2 – false case substitute
	 * @param  array $processing		Array of functions to call on value.
	 * @param  array $replacements		Array of replacements.
	 * 
	 * @return mixed
	 * 
	 */

	public static function e($global, $name, $type, $if = [], $processing = [], $replacements = [])
	{
		$data = Input::$global($name, $type);

		/* if */

		if (!empty($if)) {
			if ($data === $if[0]) {
				$data = $if[1];
			} else {
				if (!empty($if[2])) {
					$data = $if[2];
				} else {
					$data = $data;
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

	public static function get($name, $type, $if = [], $processing = [], $replacements = [])
	{
		$data = In::e("get", $name, $type, $if, $processing, $replacements);
		return $data;
	}

	public static function post($name, $type, $if = [], $processing = [], $replacements = [])
	{
		$data = In::e("post", $name, $type, $if, $processing, $replacements);
		return $data;
	}
}
