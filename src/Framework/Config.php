<?php

namespace Startie;

class Config
{
	/**
	 * Can be: "DEVELOPMENT", "TEST", "PRODUCTION"
	 */
	public static $stage;

	/**
	 * Can be: "LOCAL", "REMOTE"
	 */
	public static $machine;

	public static function init(): void
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

		Config::dirs();
		Config::region();
	}

	/**
	 * Creates some useful global constants as: DIR_APP, BACKEND_DIR, PUBLIC_DIR, etc.
	 */

	private static function dirs(): void
	{
		// Load .env
		if (isset($_ENV['DIR_APP'])) {
			$dirRoot = $_ENV['DIR_APP'];

			// Checking DIR_APP
			if (!file_exists($dirRoot)) {
				throw new \Startie\Exception("Path 'DIR_APP' from .env doesn't exist");
			}
		} else {
			throw new \Startie\Exception("No 'DIR_APP' in .env");
		}

		// Create constants
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
	public static function region(): void
	{
		define('DATE_TIMEZONE', $_ENV['DATE_TIMEZONE']);
		define('TIMEZONE', $_ENV['TIMEZONE']);
		define('LOCALE', $_ENV['LOCALE']);

		date_default_timezone_set($_ENV['DATE_DEFAULT_TIMEZONE']);
		setlocale(LC_ALL, $_ENV['LOCALE']);
	}

	/**
	 * Helper for getting a main variable from any config file
	 */
	public static function get($path)
	{
		return require App::path("backend/Config/{$path}.php");
	}
}
