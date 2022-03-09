<?php

// class Helper 
// {
// 	public static function curl($url, $post = null)
// 	{
// 		$ch = curl_init( $url );
// 		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
// 		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417
// 		Firefox/3.0.3');
// 		if($post){
// 		  curl_setopt($ch, CURLOPT_POST, 1);
// 		  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
// 		}
// 		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
// 		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
// 		$response = curl_exec( $ch );
// 		curl_close( $ch );
// 		return $response;
// 	}	

// 	public static function isg($glob, $name)
// 	{
// 		$glob = strtoupper($glob);
		
// 		switch ($glob) {

// 			case 'COOKIE':
// 				if( isset($_COOKIE[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;

// 			case 'ENV':
// 				if( isset($_ENV[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;

// 			case 'FILES':
// 				if( isset($_FILES[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;

// 			case 'GET':
// 				if( isset($_GET[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;
			
// 			case 'POST':
// 				if( isset($_POST[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;	

// 			case 'REQUEST':
// 				if( isset($_REQUEST[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;	

// 			case 'SERVER':
// 				if( isset($_SERVER[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;	

// 			case 'SESSION':
// 				if( isset($_SESSION[$name])){
// 					return true;
// 				}
// 				return false;
// 				break;
// 		}
// 	}

// 	#
// 	#
// 	#	Description:
// 	# 	check if (super) global has a variable with ceratain value is exists
// 	#
// 	#
	
// 	public static function isge($glob, $var, $value)
// 	{
// 		$glob = strtoupper($glob);

// 		switch ($glob) {
// 			case 'COOKIE':
// 				if( isset($_COOKIE[$var]) &&  $_COOKIE[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;

// 			case 'ENV':
// 				if( isset($_ENV[$var]) &&  $_ENV[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;

// 			case 'FILES':
// 				if( isset($_FILES[$var]) &&  $_FILES[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;

// 			case 'GET':
// 				if( isset($_GET[$var]) &&  $_GET[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;

// 			case 'POST':
// 				if( isset($_POST[$var]) &&  $_POST[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;	

// 			case 'REQUEST':
// 				if( isset($_REQUEST[$var]) &&  $_REQUEST[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;	

// 			case 'SERVER':
// 				if( isset($_SERVER[$var]) &&  $_SERVER[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;	

// 			case 'SESSION':
// 				if( isset($_SESSION[$var]) &&  $_SESSION[$var] == $value){
// 					return true;
// 				} 
// 				return false;
// 				break;
// 		}
// 	}

// 	#
// 	#
// 	#	Description:
// 	# 	check if variable is set and has a certain value
// 	#
// 	#
	
// 	public static function isve($var, $val=null)
// 	{
// 		if(isset ($var)) {
// 			if($val){
// 				if ($var == $val){
// 					return true;
// 				}
// 				return false;
// 			}
// 			return false;
// 		}
// 		return false;
// 	}

// 	#
// 	#
// 	#	Description:
// 	# 	check if array has a key [with certain value]
// 	#
// 	#
	
// 	public static function isake($arr, $key, $str=null)
// 	{
// 		if(isset ($arr[$key])) {
// 			if($str){
// 				if ($key == $str){
// 					return true;
// 				}
// 				return false;
// 			}
// 			return false;
// 		}
// 		return false;
// 	}

// 	public static function pre_dump($str)
// 	{
// 		echo "<pre>";
// 		var_dump($str);
// 		echo "</pre>";
// 	}

// 	public static function dump($result, $die=0)
// 	{
// 		if
// 			(
// 				Helper::isge('SESSION', 'vk_uid', '231634207') ||
// 				Helper::isge('SESSION', 'vk_uid', '333297662') ||
// 				Helper::isge('SESSION', 'vk_uid', '34815965')
// 			)
// 		{
// 			Helper::pre_dump($result);
// 			if($die) die();
// 		}
// 	}

// 	#
// 	#
// 	#	Supports:
// 	#	- COOKIE, ENV, GET, POST, SERVER
// 	#	Doesn't supports: 
// 	#	- FILES, REQUEST, SESSION
// 	#
	
// 	public static function input($glob, $name, $type)
// 	{
// 		$glob = strtoupper($glob);

// 		if( Helper::isg($glob, $name) ){

// 			$glob = 'INPUT_' . $glob;

// 			if($type == "int"){
// 				return intval(filter_input(constant($glob), $name, FILTER_SANITIZE_NUMBER_INT));
// 			}

// 			if($type == "float"){
// 				return filter_input(constant($glob), $name, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
// 			}

// 			if ($type = "str" || $type = "string"){
// 				return filter_input(constant($glob), $name, FILTER_SANITIZE_STRING);
// 			}

// 			if ($type = "email"){
// 				return filter_input(constant($glob), $name, FILTER_SANITIZE_EMAIL);
// 			}

// 			if ($type = "url"){
// 				return filter_input(constant($glob), $name, FILTER_SANITIZE_URL);
// 			}

// 			if ($type = "raw"){
// 				return filter_input(constant($glob), $name, FILTER_UNSAFE_RAW);
// 			}
// 		}
// 		return false;
// 	}

// }