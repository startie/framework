<?php

namespace Startie;

class Config
{
	public static $stage; // DEVELOPMENT/TEST/PRODUCTION
	public static $machine; // LOCAL/REMOTE

	public static function init()
	{
		if (isset($_ENV['MACHINE'])) {
			self::$machine = $_ENV['MACHINE'];
		} else {
			throw new \Startie\Exception('No "MACHINE" in .env');
		}

		if (isset($_ENV['STAGE'])) {
			self::$stage = $_ENV['STAGE'];
		} else {
			throw new \Startie\Exception('No "STAGE" in .env');
		}

		Config::dirs();
		Config::region();
	}

	/* 
	 *	Creates some useful global constants as:
	 * 	- DIR_APP
	 * 
	 * 	- BACKEND_DIR
	 * 	– PUBLIC_DIR
	 * 	- ...
	 */

	public static function dirs()
	{
		// Load

		if (isset($_ENV['DIR_APP'])) {
			$dirRoot = $_ENV['DIR_APP'];
		} else {
			throw new Exception("Enviroment 'DIR_APP' is missing");
		}

		// Constants

		define("DIR_APP", $dirRoot);
		define("FRONTEND_DIR", DIR_APP . "frontend/");
		define("BACKEND_DIR", DIR_APP . "backend/");
		define("PUBLIC_DIR", DIR_APP . "public/");
		define("STORAGE_DIR", DIR_APP . "storage/");
		define("VENDOR_DIR", DIR_APP . "vendor/");
	}

	public static function region()
	{
		date_default_timezone_set($_ENV['DATE_DEFAULT_TIMEZONE']);
		define('DATE_TIMEZONE', $_ENV['DATE_TIMEZONE']);
		define('TIMEZONE', $_ENV['TIMEZONE']);
		define('LOCALE', $_ENV['LOCALE']);
		setlocale(LC_ALL, $_ENV['LOCALE']);
	}

	public static function get($path)
	{
		return require App::path("backend/Config/{$path}.php");
	}
}
