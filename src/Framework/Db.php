<?php

namespace Startie;

class Db
{
	public static $h;

	public function __construct()
	{
		$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4;';

		try {
			DB::$h = new \PDO($dsn, DB_USER, DB_PASSWORD);
			DB::$h->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			print "Error: " . $e->getMessage() . "<br/>";
			die();
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
