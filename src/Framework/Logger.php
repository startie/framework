<?php

// class Logger 
// {
// 	public static $file = BACKEND_LOGS_DIR . 'errors/php.json';

// 	# # # # # # # # # # # # # # # # # # # # # # #
// 	#											
// 	#		INIT, ADD, INSERT, GENERATE, CREATE
// 	#											
// 	# # # # # # # # # # # # # # # # # # # # # # #
	
// 	public static function i($msg, $line="", $file=""){

// 		//Open the file to get existing content
// 		$current = file_get_contents(self::$file);

// 		$json = json_decode($current, TRUE);
		
// 		// Append a message
// 		$json[] = self::generate($msg, $line, $file);
// 		$json = json_encode($json);

// 		// Write the contents back to the file
// 		file_put_contents(self::$file, $json);
// 	}

// 	public static function generate($msg, $line, $file="")
// 	{
// 		$date = date('Y-m-d H:i:s');
// 		$line = $line;
// 		$file = $file;
// 		$url = $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];
// 		$msg = $msg;

// 		$item = [
// 			"date" => $date,
// 			"line" => $line,
// 			"file" => $file,
// 			"url" => $url,
// 			"message" => $msg,
// 		];

// 		// $m = "";
// 		// $m .= "––––––––––––––––––––––––––––\n\n";
// 		// $m .= "DATE: " . $date . "\n"; 
// 		// $m .= "SCRIPT: " . $line  . " – " . $script . "\n"; 
// 		// $m .= "MESSAGE: " . $msg . "\n\n";

// 		return $item;
// 	}

// 	# # # # # # # # # # # # # # # # # # # # # # #
// 	#											
// 	#		EDIT, UPDATE, CHANGE
// 	#											
// 	# # # # # # # # # # # # # # # # # # # # # # #

// 	# # # # # # # # # # # # # # # # # # # # # # #
// 	#											
// 	#		GET, CHECK
// 	#											
// 	# # # # # # # # # # # # # # # # # # # # # # #

// 	# # # # # # # # # # # # # # # # # # # # # # #
// 	#											
// 	#		DELETE, REMOVE, CLEAR
// 	#											
// 	# # # # # # # # # # # # # # # # # # # # # # #

// 	# # # # # # # # # # # # # # # # # # # # # # #
// 	#											
// 	#		RENDER, SHOW, DISPLAY
// 	#											
// 	# # # # # # # # # # # # # # # # # # # # # # #
	
// }