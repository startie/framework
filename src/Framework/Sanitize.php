<?php

namespace Startie;

class Sanitize
{
	/**
	 * Sanitizes a value to integer or zero.
	 * Removes illegal characters.
	 * 
	 * ```php
	 * Sanitize::int("") // 0
	 * Sanitize::int(NULL) // 0
	 * ```
	 *
	 * @param  mixed $var
	 * @return integer
	 * @tested
	 */
	public static function int($var): int
	{
		return intval(
			filter_var($var, FILTER_SANITIZE_NUMBER_INT)
		);
	}
	public static function integer(mixed $var): int
	{
		return self::int($var);
	}

	/**
	 * Sanitizes a value to float or the empty string.
	 *
	 * ```php
	 * Sanitize::float(NULL) // 0
	 * Sanitize::float("0.2") // 0,2
	 * Sanitize::float([]) // 0
	 * Sanitize::float("text") // 0
	 * Sanitize::float("2") // 2
	 * ```
	 * 
	 * @param  mixed $var
	 * @return float
	 * 
	 * TODO: test
	 */
	public static function float($var): float
	{
		$filtered = filter_var(
			$var,
			FILTER_SANITIZE_NUMBER_FLOAT,
			FILTER_FLAG_ALLOW_FRACTION
		);

		$converted = floatval($filtered);

		return $converted;
	}

	// TODO: test
	public static function double(mixed $var): float
	{
		return self::float($var);
	}

	/**
	 * Sanitizes a value to string.
	 * 
	 * ```php
	 * Sanitize::str(NULL) // ""
	 * ```
	 *
	 * @param  mixed $var
	 * @return string
	 * 
	 * TODO: test
	 */
	public static function str($var): string
	{
		return filter_var($var, FILTER_UNSAFE_RAW);
	}

	public static function string($var): string
	{
		return self::str($var);
	}

	/**
	 * Sanitizes a value to email or empty string.
	 *
	 * ```php
	 * Sanitize::email(NULL) // ""
	 * ```
	 * 
	 * @param  mixed $var
	 * @return string
	 * 
	 * TODO: test
	 */
	public static function email($var): string
	{
		return filter_var($var, FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Sanitizes a value url or empty string.
	 *
	 * ```
	 * Sanitize::url(NULL) // ""
	 * ```
	 * 
	 * @param  mixed $var
	 * @return string
	 * 
	 * TODO: test
	 */
	public static function url($var): string
	{
		return filter_var($var, FILTER_SANITIZE_URL);
	}

	/**
	 * Doesn't sanitize.
	 *
	 * ```
	 * Sanitize::raw(NULL) // NULL
	 * ```
	 * 
	 * @param  mixed $var
	 * @return mixed
	 * 
	 * TODO: test
	 */
	public static function raw($var): mixed
	{
		return $var;
	}
}