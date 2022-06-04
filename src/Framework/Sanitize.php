<?php

namespace Startie;

class Sanitize
{
	// "" => "0"
	// NULL => "0"
	// Sanitize::int(NULL) // "0"

	public static function int($var)
	{
		return intval(filter_var($var, FILTER_SANITIZE_NUMBER_INT));
	}

	// Sanitize::float(NULL) // ""

	public static function float($var)
	{
		return filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}

	// Sanitize::str(NULL) // ""

	public static function str($var)
	{
		return filter_var($var, FILTER_UNSAFE_RAW);
	}

	// Sanitize::email(NULL) // ""

	public static function email($var)
	{
		return filter_var($var, FILTER_SANITIZE_EMAIL);
	}

	// Sanitize::url(NULL) // ""

	public static function url($var)
	{
		return filter_var($var, FILTER_SANITIZE_URL);
	}

	// Sanitize::raw(NULL) // NULL

	public static function raw($var)
	{
		return $var;
	}
}
