<?php

namespace Startie;

class Input
{
	use \Startie\Bootable;

	public static function boot(): void
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static string $SanitizeTypeDefault;

	public static function loadConfig(): void
	{
		$configPath = App::path("backend/Config/Input/*.php");

		if (file_exists($configPath)) {
			self::$config = require($configPath);
			if (!isset(self::$config['SanitizeTypeDefault'])) {
				throw new Exception(
					"'SanitizeTypeDefault' is not defined in config for 'Startie\Input'"
				);
			}
		} else {
			throw new Exception(
				"Config path for `Input` was not found: "
					. $configPath
			);
		}
	}

	/**
	 * Checks if the superglobal array certain key.
	 *
	 * @param string $glob
	 * @param string $name
	 * @return boolean
	 */
	public static function is(string $glob, string $name)
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

	public static function isEmpty(mixed $glob, array $exclude): bool|int
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

	private static function g(
		mixed $var,
		string $SanitizeType,
		mixed $glob
	): mixed {
		if (!$SanitizeType) {
			$SanitizeType = self::$config['SanitizeTypeDefault'];
		};

		$value = $glob[$var] ?? NULL;

		// if (is_array($value) && $glob === $_GET) {
		// 	$result = "";
		// 	foreach ($value as $item) {
		// 		$result .= call_user_func(
		// 			'Startie\Sanitize::' . $SanitizeType,
		// 			$item
		// 		);
		// 	}
		// } else {
		$result = call_user_func(
			'Startie\Sanitize::' . $SanitizeType,
			$value
		);
		// }

		return $result;
	}

	/**
	 * Gets a value from $_COOKIE variable specified by key
	 * is required by Model::isWhereInput
	 */
	public static function cookie(
		mixed $var,
		string $SanitizeType
	): mixed {
		self::requireBoot();
		return self::g($var, $SanitizeType, $_COOKIE);
	}

	/**
	 * Gets a value from $_ENV variable specified by key
	 * is required by Model::isWhereInput
	 */
	public static function env(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_ENV);
	}

	/**
	 * Gets a value from $_FILES variable specified by key
	 * is required by Model::isWhereInput
	 */
	public static function files(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_FILES);
	}

	/**
	 * Gets a value from $_GET variable specified by key
	 * is required by Model::isWhereInput
	 */
	public static function get(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();

		return self::g($var, $SanitizeType, $_GET);
	}

	/**
	 * Gets a value from $_POST variable specified by key
	 * is required by Model::isWhereInput
	 */
	public static function post(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_POST);
	}

	/**
	 * Gets a value from $_REQUEST variable specified by key
	 * is required by Model::isWhereInput
	 */
	public static function request(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_REQUEST);
	}

	/**
	 * Gets a value from $_SERVER variable by key
	 * is required by Model::isWhereInput
	 */
	public static function server(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_SERVER);
	}

	/**
	 * Gets a value from $_SESSION variable by key
	 * is required by Model::isWhereInput
	 */
	public static function session(mixed $var, string $SanitizeType): mixed
	{
		self::requireBoot();
		return self::g($var, $SanitizeType, $_SESSION);
	}
}