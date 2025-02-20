<?php

namespace Startie;

use Startie\Config;

class Asseter
{
	public static string $jsPrefix;
	public static string $cssPrefix;
	public static string|null $root;
	public static string $hash = "";

	public static function init()
	{
		self::loadConfig();
	}

	/**
	 * @throws \Exception
	 */
	public static function loadConfig(): void
	{
		$config = Config::get("Asseter");

		if (isset($config['prefixes'])) {
			if (isset($config['prefixes']['js'])) {
				self::$jsPrefix = $config['prefixes']['js'];
			} else {
				throw new \Exception(
					"`js` prefix for Asseter is not configured"
				);
			}

			if (isset($config['prefixes']['css'])) {
				self::$cssPrefix = $config['prefixes']['css'];
			} else {
				throw new \Exception(
					"`css` prefix for Asseter is not configured"
				);
			}
		} else {
			throw new \Exception("`prefixes` for Asseter is not configured");
		}

		if (isset($config['root'])) {
			self::$root = $config['root'];
		} else {
			throw new \Exception("`root` for Asseter is not configured");
		}
	}

	public static function resolveHash(string $fromAssetType = "js"): string
	{
		if (self::$hash !== "") {
			return self::$hash;
		}

		$PUBLIC_JS_DIR = PUBLIC_DIR . "$fromAssetType/";
		$typeFiles = scandir($PUBLIC_JS_DIR);

		if ($typeFiles === false) {
			throw new Exception(
				"No $fromAssetType files for finding hash on $PUBLIC_JS_DIR"
			);
		}

		$hash = "";
		$listOfFiles = array_diff($typeFiles, ['..', '.']);

		if (empty($listOfFiles)) {
			throw new Exception(
				"No {$fromAssetType} files for finding hash on $PUBLIC_JS_DIR"
			);
		}

		$listOfFilesNew = [];
		foreach ($listOfFiles as $file) {
			if (strpos($file, ".{$fromAssetType}")) {
				$listOfFilesNew[] = $file;
			}
		}

		if (empty($listOfFilesNew)) {
			throw new Exception(
				"No {$fromAssetType} files for finding hash on {$PUBLIC_JS_DIR}"
			);
		}

		if (count($listOfFilesNew) > 1) {
			$lastFileOfType = $listOfFilesNew[count($listOfFilesNew) - 1];
		} else if (count($listOfFilesNew) === 1) {
			$lastFileOfType = $listOfFilesNew[0];
		} else {
			throw new Exception(
				"No {$fromAssetType} files for finding hash on {$PUBLIC_JS_DIR}"
			);
		}

		preg_match(
			"/([a-z0-9]*)\.({$fromAssetType})/",
			$lastFileOfType,
			$matches
		);

		$hash = $matches[1];

		self::$hash = $hash;
		return $hash;
	}

	public static function getRootUrl(): string
	{
		if (isset(self::$root)) {
			return trim(URL_APP, "/") . self::$root;
		} else {
			return trim(URL_APP, "/") . "/";
		}
	}

	public static function loadJs(string $entry): void
	{
		$prefix = self::$jsPrefix;
		$hash = self::resolveHash();
		$filename = URL_APP . self::root . "js/{$entry}{$prefix}.{$hash}.js";
		echo "<script src='$filename'></script>";
	}

	public static function loadPageJs(string|null $entry = NULL)
	{
		$prefix = self::$jsPrefix;
		$hash = self::resolveHash();

		// TODO: Find right index

		if (!$entry) {
			$controllerClass = debug_backtrace()[2]['class'];
			$controllerClass = str_replace("_Controller", "", $controllerClass);

			$controllerFunction = debug_backtrace()[2]['function'];
			$controllerFunction = ucfirst($controllerFunction);

			$filePath = "js/Pages{$controllerClass}{$controllerFunction}{$prefix}.{$hash}.js";
		} else {
			$filePath = "js/Pages{$entry}{$prefix}.{$hash}.js";
		}

		$fileDir = PUBLIC_DIR . $filePath;

		if (file_exists($fileDir)) {
			$assetUrl = self::getRootUrl() . $filePath;
			echo "<script src='{$assetUrl}'></script>";
		} else {
			throw new Exception("No file in `$fileDir`");
		}
	}

	public static function loadCss(string $entry): void
	{
		$prefix = self::$cssPrefix;
		$hash = self::resolveHash();
		$assetUrl = self::getRootUrl() . "css/{$entry}{$prefix}.{$hash}.css";
		echo "<link rel='stylesheet' href='{$assetUrl}'>";
	}

	public static function loadPageCss(string $entry): void
	{
		$prefix = self::$cssPrefix;
		$hash = self::resolveHash();
		$assetUrl = self::getRootUrl() . "css/Pages{$entry}{$prefix}.{$hash}.css";
		echo "<link rel='stylesheet' href='{$assetUrl}'>";
	}

	public static function loadRewriteCss(string $entry): void
	{
		$prefix = self::$cssPrefix;
		$hash = self::resolveHash();
		$assetUrl = self::getRootUrl() . "css/Rewrite{$entry}{$prefix}.{$hash}.css";
		echo "<link rel='stylesheet' href='{$assetUrl}'>";
	}

	public static function isExternal(string $path): bool
	{
		if (
			str_starts_with($path, "https://")
			||
			str_starts_with($path, "http://")
		) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @deprecated
	 */
	public static function getJsHash(): string
	{
		return self::resolveHash();
	}
}