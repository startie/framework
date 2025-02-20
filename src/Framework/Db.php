<?php

declare(strict_types=1);

namespace Startie;

class Db
{
	use \Startie\Bootable;

	public static array $connections = [];
	public static array $excludeFunctions;

	public static function boot(): void
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static function loadConfig(): void
	{
		Db::$config = Config::get("Db");
	}

	/**
	 * Load config, make connections and store them
	 */
	public static function init(): void
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
			$functionsToExclude = $_ENV[$envindex];
			if (is_string($functionsToExclude)) {
				Db::$excludeFunctions = explode(",", $functionsToExclude);
			}
		}
	}

	/**
	 * Returns empty string when param doesn't exists in config
	 */
	public static function config(string|null $name = null): string
	{
		if (isset($name)) {
			return Db::$config[$name];
		} else {
			return "";
		}
	}

	public static function connect(string $dsn, string $driver): \PDO|null
	{
		if ($driver === "mysql") {
			try {
				$connection = new \PDO($dsn);
				$connection->setAttribute(
					\PDO::ATTR_ERRMODE,
					\PDO::ERRMODE_EXCEPTION
				);

				return $connection;
			} catch (\PDOException $e) {
				$errorText = "Failed to connect to database." . PHP_EOL;
				Logger::toFile($e . PHP_EOL . PHP_EOL);

				if (Dev::isSecretMode()) {
					$errorText .= "Hi developer!"
						. "Check your connection credentionals "
						. "and if the database is running."
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
	 */
	public static function dsn(array $params): string
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
				if (!isset($path)) {
					throw new Exception("Path is required for SQLite");
				}
				$dsn = "sqlite:$path";
				break;

			case 'mysql':
				$dsn = "mysql:host=$host;port=$port;dbname=$name;"
					. "charset=$charset;user=$user;password=$password";

				break;

			case 'pgsql':
				$dsn = "pgsql:host=$host;port=$port;dbname=$name;"
					. "charset=$charset;user=$user;password=$password";

			default:
				throw new \Startie\Exception("Unsupported DB driver: $driver");
		}

		return $dsn;
	}

	public static function debugStart(
		bool|int $debug = false,
		mixed $object = null
	): void {
		if ((bool) $debug === false) {
			Dump::start($object);
		}
	}

	public static function debugContinue(
		bool|int $debug = false,
		mixed $object = null
	): void {
		if ((bool) $debug === false) {
			Dump::next($object);
		}
	}

	public static function debugEnd(
		bool|int $debug = false,
		mixed $object = null
	): void {
		if ((bool) $debug === false) {
			Dump::end($object);
		}
	}

	public static function debug(
		bool|int $debug = false,
		mixed $object = null,
		string $message = ""
	): void {
		if ((bool) $debug) {
			Dump::make($object, $die = false, $message);
		}
	}
}