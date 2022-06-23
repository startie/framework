<?php

namespace Startie;

class Asseter
{

	public static $jsPrefix;
	public static $cssPrefix;

	public static function config()
	{
		$path = App::path("backend/Config/Asseter/Common.php");

		if (!file_exists($path)) {
			throw new Exception("Config for Asseter is missing");
		} else {
			$Config = require($path);
			$ConfigPrefixes = $Config['prefixes'];

			if (isset($ConfigPrefixes['js'])) {
				self::$jsPrefix = $ConfigPrefixes['js'];
			} else {
				throw new Exception("JS prefix is not defined");
			}

			if (isset($ConfigPrefixes['css'])) {
				self::$jsPrefix = $ConfigPrefixes['css'];
			} else {
				throw new Exception("CSS prefix is not defined");
			}
		}
	}

	public static function init()
	{
		self::config();
	}

	public static function getJsHash()
	{
		$listOfFiles = array_diff(scandir(PUBLIC_DIR . "/js"), array('..', '.'));
		foreach ($listOfFiles as $file) {
			if (strpos($file, '.js')) {
				$listOfFilesNew[] = $file;
			}
		}
		$listOfFiles = $listOfFilesNew;
		$lastJSFile = $listOfFilesNew[count($listOfFilesNew) - 1];
		preg_match('/([a-z0-9]*)\.(js)/', $lastJSFile, $m);
		$JShash = $m[1];
		return $JShash;
	}

	public static function loadJs($bundle)
	{
		$prefix = self::$jsPrefix;
		$hash = Asseter::getJsHash();
		$filename = PUBLIC_URL . "js/" . $bundle . $prefix . "." . $hash . ".js";
		echo "<script src='$filename'></script>";
	}

	public static function loadPageJs($bundle = null)
	{
		$prefix = self::$jsPrefix;
		$hash = Asseter::getJsHash();

		#todo Find right index

		if (!$bundle) {
			$controllerClass = debug_backtrace()[2]['class'];
			$controllerClass = str_replace("_Controller", "", $controllerClass);

			$controllerFunction = debug_backtrace()[2]['function'];
			$controllerFunction = ucfirst($controllerFunction);

			$filePath = "js/Pages"  . $controllerClass . $controllerFunction . $prefix . "." . $hash . ".js";
		} else {
			$filePath = "js/Pages"  . $bundle . $prefix . "." . $hash . ".js";
		}

		$fileDir = PUBLIC_DIR . $filePath;

		if (file_exists($fileDir)) {
			echo "<script                 src='" . PUBLIC_URL . $filePath . "'></script>";
		} else {
			echo $fileDir;
		}
	}

	public static function loadCss($bundle)
	{
		$prefix = self::$cssPrefix;
		$hash = Asseter::getJsHash();
		echo "<link rel='stylesheet' href='" . PUBLIC_URL . "css/"      . $bundle . $prefix . "." . $hash . ".css'>";
	}

	public static function loadPageCss($bundle)
	{
		$prefix = self::$cssPrefix;
		$hash = Asseter::getJsHash();
		echo "<link rel='stylesheet' href='" . PUBLIC_URL . "css/Pages" . $bundle . $prefix . "." . $hash . ".css'>";
	}

	public static function loadRewriteCss($bundle)
	{
		$prefix = self::$cssPrefix;
		$hash = Asseter::getJsHash();
		echo "<link rel='stylesheet' href='" . PUBLIC_URL . "css/Rewrite" . $bundle . $prefix . "." . $hash . ".css'>";
	}
}
