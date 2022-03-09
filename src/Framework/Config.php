<?php

class Config 
{	
	public static $stage; # DEVELOPMENT, TEST, PRODUCTION
	public static $machine; # LOCAL, REMOTE

	public static function init()
	{
		if(isset($_ENV['MACHINE'])){
			self::$machine = $_ENV['MACHINE'];
		} else {
			die('No "MACHINE" in .env');
		}

      if(isset($_ENV['STAGE'])){
      	self::$stage = $_ENV['STAGE'];
      } else {
			die('No "STAGE" in .env');
		}

		Config::initMain();
		Config::initDirsAndUrls();
		Config::initRegion();
	}

	public static function initMain()
	{
		$address = $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		$protocol = $_ENV['PROTOCOL'];
		$server = $_ENV['SERVERNAME'] . $_ENV['SERVERPORT'];
		$dirRoot = $_ENV['DIR_APP'];
		$machine = $_ENV['MACHINE'];

		$domain = $_ENV['DOMAIN'];
		$stage = $_ENV['STAGE'];
		
		define("APP_PROTOCOL", $protocol);
		define("URL_APP", $protocol . $server . $domain); 
		define("DIR_APP", $dirRoot);
		
		define("DB_HOST", 		$_ENV["DB_HOST"]);
		define("DB_NAME", 		$_ENV["DB_NAME"]);
		define("DB_USER", 		$_ENV["DB_USER"]);
		define("DB_PASSWORD", 	$_ENV["DB_PASSWORD"]);
	}

	public static function initDirsAndUrls()
	{
		$backend = dirname(dirname(__DIR__));
		$backendArr = explode('/', $backend);
		$projectDirCount = count($backendArr) - 2; #todo 21

		$projectDirPath = '/' . implode('/', array_slice($backendArr, 1, $projectDirCount));

		$projectFolders = File::getFolders($projectDirPath);
		#var_dump($projectFolders); die();
				
		#
		# 	DIRS & URLS #todo 20
		# 
	
		foreach ($projectFolders as $projectFolder) {

			#var_dump($projectFolder); die();

			#
			# 	DIRS
			# 
			
			$constFolderName = strtoupper($projectFolder) . "_DIR";
			define($constFolderName, DIR_APP . $projectFolder . "/");

			#
			# 	SUBDIRS
			# 
			
			$projectSubFolderPath = DIR_APP . $projectFolder;
			$projectSubFolders = File::getFolders($projectSubFolderPath);
			foreach ($projectSubFolders as $projectSubFolder) {
				$constSubFolderName = strtoupper($projectFolder) . "_" . strtoupper($projectSubFolder) . "_DIR";
				define($constSubFolderName, DIR_APP . $projectFolder . "/" . $projectSubFolder . "/");
			}

			#
			# 	URLS
			# 
			
			$constUrlName = strtoupper($projectFolder) . "_URL";
			define($constUrlName, URL_APP . $projectFolder . "/");

			#
			# 	SUBURL
			# 
			
			$projectSubUrlPath = DIR_APP . $projectFolder;
			$projectSubUrls = File::getFolders($projectSubUrlPath);
			foreach ($projectSubUrls as $projectSubUrl) {
				$constSubUrlName = strtoupper($projectFolder) . "_" . strtoupper($projectSubUrl) . "_URL";
				define($constSubUrlName, URL_APP . $projectFolder . "/" . $projectSubUrl . "/");
			}
			
		}
		#die();
	}

	public static function initRegion()
	{
		date_default_timezone_set($_ENV['DATE_DEFAULT_TIMEZONE']);
		define('DATE_TIMEZONE', $_ENV['DATE_TIMEZONE']);
		define('TIMEZONE',$_ENV['TIMEZONE']);
		define('LOCALE', $_ENV['LOCALE']);
		setlocale(LC_ALL, $_ENV['LOCALE']);
	}

}