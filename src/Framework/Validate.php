<?php

namespace Startie;

class Validate
{
	public static function bool($var)
	{
		return intval(filter_var($var, FILTER_VALIDATE_BOOLEAN));
	}

	public static function int($var)
	{
		return is_int($var);
	}

	public static function intStr($var)
	{
		return filter_var($var, FILTER_VALIDATE_INT);
	}

	public static function integer($var)
	{
		Validate::int($var);
	}

	public static function number($var)
	{
		Validate::int($var);
	}

	public static function numeric($var)
	{
		return is_numeric($var);
	}

	public static function float($var)
	{
		$temp_var = str_replace(".", ",", $var);
		return filter_var($temp_var, FILTER_VALIDATE_FLOAT, array('options' => array('decimal' => ',')));
	}

	public static function ip($var)
	{
		return filter_var($var, FILTER_VALIDATE_IP);
	}

	public static function mac($var)
	{
		return filter_var($var, FILTER_VALIDATE_MAC);
	}

	public static function str($var)
	{
		return is_string($var);
	}

	public static function string($var)
	{
		Validate::str($var);
	}

	public static function email($var)
	{
		return filter_var($var, FILTER_VALIDATE_EMAIL);
	}

	public static function url($var)
	{
		return filter_var($var, FILTER_VALIDATE_URL);
	}

	public static function regexp($var)
	{
		return filter_var($var, FILTER_VALIDATE_REGEXP);
	}
}
