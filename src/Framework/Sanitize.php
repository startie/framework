<?php

class Sanitize 
{
	public static function int($var)
 	{
 		return intval(filter_var($var, FILTER_SANITIZE_NUMBER_INT));
 	}

 	public static function float($var)
 	{
 		return filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
 	}

 	public static function str($var)
 	{
 		return filter_var($var, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
 	}

 	public static function email($var)
 	{
 		return filter_var($var, FILTER_SANITIZE_EMAIL);
 	}

 	public static function url($var)
 	{
 		return filter_var($var, FILTER_SANITIZE_URL);
 	}

 	public static function raw($var)
 	{
 		return $var;	
 	}
}