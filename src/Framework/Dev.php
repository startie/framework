<?php

namespace Startie;

class Dev
{
	public static $start = 0;
	public static $stop = 0;

	public static function start()
	{
		static::$start = 0;
		static::$start = microtime(true);
	}

	public static function stop()
	{
		static::$stop = 0;
		static::$stop = microtime(true);
		$time = microtime(true) - static::$start;
		echo $time;
		echo "<br>";
	}

	/**
	 * @param array $global Global array
	 */
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

	public static function isSecretMode()
	{
		if ($_ENV['MODE_DEV']) {
			if (isset($_ENV['DEV_SECRET_MODE_KEY'])) {
				if (isset($_REQUEST['DEV_SECRET_MODE_KEY'])) {
					return
						$_ENV['DEV_SECRET_MODE_KEY']
						===
						$_REQUEST['DEV_SECRET_MODE_KEY'];
				}
			}
		}

		return false;
	}

	/**
	 * @deprecated
	 */
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

	public static function counter($start_time)
	{
		$result = "";

		if (Access::is('developers') || Dev::isSecretMode()) {
			// TODO: delete bootstrap dependecy
			$result .= "<div id='DevLoadCounter' class='container-fluid text-muted'>";
			$result .= number_format(microtime(true) - $start_time, 2) . "s";
			$result .= "</div>";
		}

		return $result;
	}
}