<?php

namespace Startie;

class Input
{
	use \Startie\Bootable;

	public static $SanitizeTypeDefault;

	public static function boot()
	{
		self::$isBooted = true;
		self::config();
	}

	public static function config()
	{
		$path = App::path("backend/Config/Input/*.php");

		if (!file_exists($path)) {
			throw new Exception("Config for 'Startie\Input' is missing");
		} else {
			$Config = require($path);
			if (isset($Config['SanitizeTypeDefault'])) {
				Input::$SanitizeTypeDefault = strtoupper($Config['SanitizeTypeDefault']);
			} else {
				throw new Exception("'SanitizeTypeDefault' is not defined in config for 'Startie\Input'");
			}
		}
	}

	/**
	 * Checks if the superglobal array certain key.
	 *
	 * @param  string $glob
	 * @param  string $name
	 * @return boolean
	 */
	public static function is($glob, $name)
	{
		self::requireBoot();

		$glob = strtoupper($glob);

		switch ($glob) {

			case 'COOKIE':
				if (isset($_COOKIE[$name])) {
					return true;
				}
				return false;

			case 'ENV':
				if (isset($_ENV[$name])) {
					return true;
				}
				return false;

			case 'FILES':
				if (isset($_FILES[$name])) {
					return true;
				}
				return false;

			case 'GET':
				if (isset($_GET[$name])) {
					return true;
				}
				return false;

			case 'POST':
				if (isset($_POST[$name])) {
					return true;
				}
				return false;

			case 'REQUEST':
				if (isset($_REQUEST[$name])) {
					return true;
				}
				return false;

			case 'SERVER':
				if (isset($_SERVER[$name])) {
					return true;
				}
				return false;

			case 'SESSION':
				if (isset($_SESSION[$name])) {
					return true;
				}
				return false;
		}

		return false;
	}

	public static function isEmpty($glob, $exclude)
	{
		self::requireBoot();

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

	private static function g($var, $SanitizeType, $glob)
	{
		if (!$SanitizeType) {
			$SanitizeType = Input::$SanitizeTypeDefault;
		};

		$result = call_user_func(
			'Startie\Sanitize::' . $SanitizeType,
			$glob[$var] ?? NULL
		);

		return $result;
	}

	public static function cookie($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_COOKIE);
	}

	public static function env($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_ENV);
	}

	public static function files($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_FILES);
	}

	public static function get($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_GET);
	}

	public static function post($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_POST);
	}

	public static function request($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_REQUEST);
	}

	public static function server($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_SERVER);
	}

	public static function session($var, $SanitizeType)
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_SESSION);
	}
}
