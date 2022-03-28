<?php

namespace Startie;

class Input
{
	public static $inputTypeDefault;

	public static function init()
	{
		if (isset($_ENV['INPUT_TYPE_DEFAULT'])) {
			self::$inputTypeDefault = $_ENV['INPUT_TYPE_DEFAULT'];
		}
	}

	#
	#
	#	Description:
	# 	- checks if (super) global array has variable
	#
	#

	public static function is($glob, $name)
	{
		$glob = strtoupper($glob);

		switch ($glob) {

			case 'COOKIE':
				if (isset($_COOKIE[$name])) {
					return true;
				}
				return false;
				break;

			case 'ENV':
				if (isset($_ENV[$name])) {
					return true;
				}
				return false;
				break;

			case 'FILES':
				if (isset($_FILES[$name])) {
					return true;
				}
				return false;
				break;

			case 'GET':
				if (isset($_GET[$name])) {
					return true;
				}
				return false;
				break;

			case 'POST':
				if (isset($_POST[$name])) {
					return true;
				}
				return false;
				break;

			case 'REQUEST':
				if (isset($_REQUEST[$name])) {
					return true;
				}
				return false;
				break;

			case 'SERVER':
				if (isset($_SERVER[$name])) {
					return true;
				}
				return false;
				break;

			case 'SESSION':
				if (isset($_SESSION[$name])) {
					return true;
				}
				return false;
				break;
		}
	}

	public static function isEmpty($glob, $exclude)
	{
		# $exclude = [
		# 	'name1', 'name2'
		# ]; 

		$isGetEmpty = 0;
		$InputGet = $_GET;

		foreach ($exclude as $e) {
			unset($InputGet[$e]);
		}

		if ($InputGet) {
			$isGetEmpty = 1;
		}

		return $isGetEmpty;
	}

	public static function cookie($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_COOKIE[$var]
		);
	}

	public static function env($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_ENV[$var]
		);
	}

	public static function files($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_FILES[$var]
		);
	}

	public static function get($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_GET[$var]
		);
	}

	public static function post($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_POST[$var]
		);
	}

	public static function request($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_REQUEST[$var]
		);
	}

	public static function server($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_SERVER[$var]
		);
	}

	public static function session($var, $type)
	{
		if (!$type) {
			$type = self::$inputTypeDefault;
		};
		return call_user_func(
			'Startie\Sanitize::' . $type,
			$_SESSION[$var]
		);
	}
}
