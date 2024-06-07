<?php

namespace Startie;

class Config
{
	/**
	 * @var string $stage. Possible values: "DEVELOPMENT", "TEST", "PRODUCTION"
	 */
	public static $stage;

	/**
	 * @var string $machine. Possible values: "LOCAL", "REMOTE"
	 */
	public static $machine;

	public static function init(): void
	{
		Config::loadEnv();
		Config::defineFilesystemConstants();
		Config::defineRegionConstants();
	}

	public static function get($name)
	{
		$stage = strtolower(Config::$stage);
		$machine = strtolower(Config::$machine);

		$name = ucfirst(strtolower($name));

		$pathStart = "backend/Config/$name";

		$path = App::path("$pathStart/{$stage}_{$machine}.php");
		$path = is_file($path) ? $path : App::path("$pathStart/*.php");
		$path = is_file($path) ? $path : App::path("$pathStart/Common.php");
		
		if (!is_file($path)) {
			throw new \Exception("Config path for `$name` was not found" . $path);
		} else {
			return require $path;
		}
	}

	public static function loadEnv()
	{
		if (isset($_ENV['MACHINE'])) {
			self::$machine = $_ENV['MACHINE'];
		} else {
			throw new \Startie\Exception("No 'MACHINE' in .env");
		}

		if (isset($_ENV['STAGE'])) {
			self::$stage = $_ENV['STAGE'];
		} else {
			throw new \Startie\Exception("No 'STAGE' in .env");
		}
	}

	/**
	 * Defines some useful global constants such as: 
	 * DIR_APP, BACKEND_DIR, PUBLIC_DIR, etc.
	 */
	private static function defineFilesystemConstants(): void
	{
		// Load .env
		if (isset($_ENV['DIR_APP'])) {
			$dirRoot = $_ENV['DIR_APP'];

			// Checking DIR_APP
			if (!file_exists($dirRoot)) {
				$dirRoot = \Startie\App::$root;
			}
		} else {
			throw new \Startie\Exception(
				"No 'DIR_APP' in .env"
			);
		}

		// Define constants
		define("DIR_APP", $dirRoot);
		define("FRONTEND_DIR", DIR_APP . "frontend/");
		define("BACKEND_DIR", DIR_APP . "backend/");
		define("PUBLIC_DIR", DIR_APP . "public/");
		define("STORAGE_DIR", DIR_APP . "storage/");
		define("VENDOR_DIR", DIR_APP . "vendor/");
	}

	/**
	 * Creates some usefule global constants and applying regional settings
	 */
	public static function defineRegionConstants(): void
	{
		define('DATE_TIMEZONE', $_ENV['DATE_TIMEZONE']);
		define('TIMEZONE', $_ENV['TIMEZONE']);
		define('LOCALE', $_ENV['LOCALE']);

		date_default_timezone_set($_ENV['DATE_DEFAULT_TIMEZONE']);
		setlocale(LC_ALL, $_ENV['LOCALE']);
	}
}
