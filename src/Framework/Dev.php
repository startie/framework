<?php

declare(strict_types=1);

namespace Startie;

class Dev
{
	public static int|float $start = 0;
	public static int|float $stop = 0;

	public static function start(): void
	{
		static::$start = 0;
		static::$start = microtime(true);
	}

	public static function stop(): void
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
	public static function renderGlobal($global): void
	{
		ksort($global, SORT_NATURAL | SORT_FLAG_CASE);

		if (!empty($global)) {
			Dump::pre($global);
		} else {
			Dump::pre([]);
		}
	}

	public static function globals(): array|null
	{
		if (!Access::is('developers')) {
			return null;
		}
		$globalsArr = [
			'$_COOKIE' => $_COOKIE,
			'$_ENV' => $_ENV,
			'$_FILES' => $_FILES,
			'$_GET' => $_GET,
			'$_POST' => $_POST,
			'$_REQUEST' => $_REQUEST,
			'$_SERVER' => $_SERVER,
			'$_SESSION' => $_SESSION,
		];

		return $globalsArr;
	}

	public static function isSecretMode(): bool
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
	public static function is(): bool
	{
		if (Access::is('developers') || $_ENV['MODE_DEV']) {
			return true;
		} else {
			return false;
		}
	}

	public static function sed(
		string $search,
		string $replace,
		string $filePath
	): void {
		$search = str_replace("/", "\/", $search);
		$replace = str_replace("/", "\/", $replace);
		$filePath = str_replace("/", "\/", $filePath);

		echo "sed -i '' 's/$search/$replace/g' $filePath";
	}

	public static function counter(int|float $start_time): string
	{
		$result = "";

		if (Access::is('developers') || Dev::isSecretMode()) {
			$result .= "<div id='DevLoadCounter' class='container-fluid text-muted'>";
			$result .= number_format(microtime(true) - $start_time, 2) . "s";
			$result .= "</div>";
		}

		return $result;
	}
}