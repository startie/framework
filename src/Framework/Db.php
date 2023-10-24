<?php

namespace Startie;

class Db
{
	use \Startie\Bootable;

	public static $connections = [];
	public static $excludeFunctions;

	public static function boot()
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static function loadConfig()
	{
		Db::$config = Config::get("Db");
	}

	/**
	 * Load config, make connections and store them
	 */
	public static function init()
	{
		self::requireBoot();

		/*
			Check and store connections
			`$type` examples: "logs", "common"
		*/

		foreach (Db::$config as $type => $params) {
			$dsn = Db::dsn($params);
			$connection = Db::connect($dsn, $params["driver"]);
			if ($connection) {
				Db::$connections[$type] = $connection;
			}
		}

		/*
			DB_EXCLUDE_FUNCTIONS
		*/

		$envindex = 'DB_EXCLUDE_FUNCTIONS';

		if (isset($_ENV[$envindex])) {
			Db::$excludeFunctions = explode(",", $_ENV[$envindex]);
		}
	}

	public static function config($name = NULL)
	{
		if (isset($name)) {
			return Db::$config[$name];
		}
	}

	public static function connect($dsn, $driver)
	{
		if ($driver === "mysql") {
			try {
				$connection = new \PDO($dsn);
				$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				return $connection;
			} catch (\PDOException $e) {
				$errorText = "Failed to connect to database." . PHP_EOL;
				Logger::toFile($e . PHP_EOL . PHP_EOL);

				if (Dev::isSecretMode()) {
					$errorText .= "Hi developer!"
						. "Check your connection credentionals and if the database is running."
						. PHP_EOL . PHP_EOL
						. $e->getMessage()
						. PHP_EOL
						. $e->getTraceAsString();
				}
				echo Errors::make($errorText);
				die();
			}
		} else {
			throw new Exception('Unsupported driver');
			return false;
		}
	}

	/**
	 * Returns DSN string
	 *
	 * PHP >=7.4
	 *
	 * @return string
	 */
	public static function dsn(array $params)
	{
		$dsn = "";

		[
			'driver' => $driver,
			'host' => $host,
			'port' => $port,
			'name' => $name,
			'user' => $user,
			'password' => $password,
			'charset' => $charset,
		] = $params;

		if (isset($params['path'])) {
			['path' => $path] = $params;
		};

		switch ($driver) {
			case 'sqlite':
				$dsn = "sqlite:$path";
				break;

			case 'mysql':
				$dsn = "mysql:host=$host;port=$port;dbname=$name;charset=$charset;user=$user;password=$password";
				break;

			case 'pgsql':
				$dsn = "pgsql:host=$host;port=$port;dbname=$name;charset=$charset;user=$user;password=$password";

			default:
				throw new \Startie\Exception("Unsupported DB driver: $driver");
		}

		return $dsn;
	}

	public static function debugStart($debug = 0, $object = NULL)
	{
		if ($debug) Dump::start($object);
	}

	public static function debugContinue($debug = 0, $object = NULL)
	{
		if ($debug) Dump::next($object);
	}

	public static function debugEnd($debug = 0, $object = NULL)
	{
		if ($debug) Dump::end($object);
	}

	public static function debug($debug = 0, $object = NULL, $msg = "")
	{
		if ($debug) Dump::make($object, $die = 0, $msg);
	}
}
