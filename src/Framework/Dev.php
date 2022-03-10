<?php

namespace Startie;

class Dev
{
	public static $start = 0;
	public static $stop = 0;

	#
	#	Vars:
	# 	$global – global's name (string)
	#

	public static function start()
	{
		static::$start = 0;
		static::$start = microtime(true);
	}

	public static function stop()
	{
		static::$stop = 0;
		static::$stop = microtime(true);
		$t = microtime(true) - static::$start;
		echo $t;
		echo "<br>";
	}

	public static function renderGlobal($global)
	{
		ksort($global, SORT_NATURAL | SORT_FLAG_CASE);

		if (!empty($global)) {
			Dump::pre($global);
		} else {
			Dump::pre([]);
		}
	}

	public static function globals()
	{
		if (Access::is('developers')) {
			$globalsArr = array(
				'$_COOKIE' => $_COOKIE,
				'$_ENV' => $_ENV,
				'$_FILES' => $_FILES,
				'$_GET' => $_GET,
				'$_POST' => $_POST,
				'$_REQUEST' => $_REQUEST,
				'$_SERVER' => $_SERVER,
				'$_SESSION' => $_SESSION,
			);
			return $globalsArr;
		}
	}

	public static function is()
	{
		if (Access::is('developers') || $_ENV['MODE_DEV']) {
			return true;
		} else {
			return false;
		}
	}

	public static function sed($search, $replace, $filePath)
	{
		$search = str_replace("/", "\/", $search);
		$replace = str_replace("/", "\/", $replace);
		$filePath = str_replace("/", "\/", $filePath);

		echo "sed -i '' 's/$search/$replace/g' $filePath";
	}
}
