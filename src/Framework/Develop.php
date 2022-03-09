<?php

// class Develop {

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		INI, ADD
	#											
	# # # # # # # # # # # # # # # # # # # # # # #
	
	// public static $start = 0;
	// public static $stop = 0;

	// public function init()
	// {
	// 	Develop::globals();
	// }

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		EDIT, UPDATE, CHANGE
	#											
	# # # # # # # # # # # # # # # # # # # # # # #

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		GET, CHECK
	#											
	# # # # # # # # # # # # # # # # # # # # # # #
	
	// public static function phpVersion()
	// {
	// 	echo 'Current PHP version: ' . phpversion();
	// }

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		DELETE, REMOVE, CLEAR
	#											
	# # # # # # # # # # # # # # # # # # # # # # #

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		RENDER, SHOW, DISPLAY
	#											
	# # # # # # # # # # # # # # # # # # # # # # #
	
	#
	#
	#	Vars:
	# 	$global – global's name (string)
	#
	#
	// public static function start(){
	// 	static::$start = 0;
	// 	static::$start = microtime(true);
	// }

	// public static function stop(){
	// 	static::$stop = 0;
	// 	static::$stop = microtime(true);
	// 	$t = microtime(true) - static::$start;
	// 	echo $t;
	// 	echo "<br>";
	// }

	// public static function t(){
	// 	$t = $stop - $start;
	// 	//echo "<pre>";
	// 	//echo date('s') . substr((string)microtime(), 1, 8);
	// 	echo $t;
	// 	
	// 	//echo "</pre>";
	// }

	// public static function renderGlobal($global)
	// {	
	// 	ksort($global, SORT_NATURAL | SORT_FLAG_CASE);
		
	// 	foreach($global as $prop => $value){
			
	// 		echo '<b>'.$prop.'</b>';
	// 		echo " => ";
	// 		if (is_array($value)) {
	// 			var_dump($value);
	// 		} else {
	// 			echo "$value \n";	
	// 		};
			
	// 	}

	// 	echo "</pre>";
	// 	echo "</div>";
	// }

	// public static function globals()
	// {
	// 	$globalsArr = array(
	// 		'$_COOKIE' => $_COOKIE,
	// 		'$_ENV' => $_ENV,
	// 		'$_FILES' => $_FILES,
	// 		'$_GET' => $_GET,
	// 		'$_POST' => $_POST,
	// 		'$_REQUEST' => $_REQUEST,
	// 		'$_SERVER' => $_SERVER,
	// 		'$_SESSION' => $_SESSION,
	// 	);

	// 	if( Access::is('developers') ){

	// 		echo "<a id='helper-toggler' class='btn' data-toggle='collapse' data-target='#hide-me'><span class='glyphicon glyphicon-wrench'></span></a>";
	// 		echo "<div id='hide-me' class='collapse out'>";

	// 		echo "<br><br>";
	// 		//echo Develop::phpVersion();
	// 		echo "<div class='container'>";
	// 		echo "<div class='row'>";

	// 		foreach ($globalsArr as $name => $value) {
	// 			echo "<div class='col-xs-3'><pre class='pre-helper'>";
	// 			echo "$name <br><br>";
	// 			Develop::renderGlobal($value);
	// 		}

	// 		echo "</div>";
	// 		echo "</div>";
	// 		echo "</div>";
			
	// 	}
	// }

// }