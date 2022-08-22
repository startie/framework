<?php

namespace Startie;

use PDOException;
use PDO;

class Db
{
	public static $h;

	public static function config($name)
	{
		$ConfigDb = require App::path("backend/Config/Db/Common.php");
		return $ConfigDb[$name];
	}

	public function __construct($ConfigName)
	{
		$DbConfig = self::config($ConfigName);
		extract($DbConfig);

		if ($driver === "mysql") {
			$dsn = "$driver:" . "host=$host;dbname=$name;charset=$charset";
		} else {
			throw new \Startie\Exception("Unsupported DB driver: $driver");
		}

		if ($driver === "mysql") {
			try {
				Db::$h = new \PDO($dsn, $user, $password);
				Db::$h->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			} catch (\PDOException $e) {
				$message = "Error when connecting to database. Check credentionals and if your database is running.";
				$message .= "<br/>";
				$message .= $e->getMessage();
				$message .= "<br/>";
				die($message);
			}
		}
	}

	public function truncateTable($table)
	{
		global $dbh;

		// Forcing truncate
		$sql = "SET FOREIGN_KEY_CHECKS = 0;";
		$sql .= "TRUNCATE TABLE $table";

		// $sql = "TRUNCATE TABLE $table";

		try {

			$dbh->exec($sql);
			//echo "Table '$table' trucated successfully!";

		} catch (PDOException $e) {

			echo $e->getMessage();
		}
	}

	public static function n($query)
	{
		return $query . "\n";
	}

	public static function debugStart($debug = 0, $object = null)
	{
		if ($debug) Dump::start($object);
	}

	public static function debugContinue($debug = 0, $object = null)
	{
		if ($debug) Dump::next($object);
	}

	public static function debugEnd($debug = 0, $object = null)
	{
		if ($debug) Dump::end($object);
	}

	public static function debug($debug = 0, $object = null, $msg = "")
	{
		if ($debug) Dump::make($object, $die = 0, $msg);
	}

	public static function showTable($table)
	{
		global $dbh;

		$sql = "SELECT * FROM $table";

		try {
			echo "<br>";
			echo "Showing content of table '$table'";
			echo "<br><br>";

			$results = $dbh->query($sql);
			echo "<pre>";
			var_dump($results->fetchAll(PDO::FETCH_ASSOC));
			echo "</pre>";
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public static function e($sql, $type = 'fetch')
	{
		global $dbh;

		try {
			$results = $dbh->query($sql);
			if ($type == 'fetch') {
				return $results->fetchAll(PDO::FETCH_ASSOC);
			}
			if ($type == 'count') {
				return $results->rowCount();
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	// public static function query($query)
	// {
	// 	global $dbh;

	// 	$sth = $dbh->prepare($query);
	// 	$sth->execute();

	// 	$result = $sth->fetchAll(PDO::FETCH_ASSOC);

	// 	return $result;
	// }

}
