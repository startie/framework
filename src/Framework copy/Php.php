<?php

namespace Startie;

class Php
{
	#
	#
	#	COMMON
	#
	#

	#
	# 	Checks if variable is set and has a certain value
	#

	public static function isve($var, $val = NULL)
	{
		if (isset($var)) {
			if ($val) {
				if ($var == $val) {
					return true;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	#
	#	Checks if two variables has smth in common
	#	todo: support for double, float
	#

	public static function hasInCommon($one, $two)
	{
		# Array
		if (is_array($one) && is_array($two)) {
			$intersect = array_intersect_key($one, $two);
			if (!empty($intersect)) {
				return true;
			} else {
				return false;
			}
		}

		# Bool
		else if (is_bool($one) && is_bool($two)) {
			if ($one == $two) {
				return true;
			} else {
				return false;
			}
		}

		# Int, float
		else if (is_numeric($one) && is_numeric($two)) {
			if ($one == $two) {
				return true;
			} else {
				return false;
			}
		}

		# Null
		else if (is_null($one) && is_null($two)) {
			return true;
		}

		# String
		else if (is_string($one) && is_string($two)) {
			if ($one == $two) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/*  
		source: https://stackoverflow.com/questions/5225971/is-it-possible-to-get-list-of-defined-namespaces
	*/

	public static function namespaceExists($namespace)
	{
		$namespace .= "\\";
		foreach (get_declared_classes() as $name)
			if (strpos($name, $namespace) === 0) return true;
		return false;
	}

	#
	#
	#	GLOBALS
	#
	#

	#
	# 	Checks if (super) global array has variable
	#

	public static function isg($glob, $name)
	{
		$glob = strtoupper($glob);

		switch ($glob) {

			case 'COOKIE':
				if (isset($_COOKIE[$name])) {
					return true;
				}
				return false;

			case 'ENV':
				if (isset($_ENV[$name])) {
					return true;
				}
				return false;

			case 'FILES':
				if (isset($_FILES[$name])) {
					return true;
				}
				return false;

			case 'GET':
				if (isset($_GET[$name])) {
					return true;
				}
				return false;

			case 'POST':
				if (isset($_POST[$name])) {
					return true;
				}
				return false;

			case 'REQUEST':
				if (isset($_REQUEST[$name])) {
					return true;
				}
				return false;

			case 'SERVER':
				if (isset($_SERVER[$name])) {
					return true;
				}
				return false;

			case 'SESSION':
				if (isset($_SESSION[$name])) {
					return true;
				}
				return false;
		}
	}

	#
	# 	Checks if (super) global has a variable with ceratain value is exists
	#

	public static function isgve($glob, $var, $value)
	{
		$glob = strtoupper($glob);

		switch ($glob) {
			case 'COOKIE':
				if (isset($_COOKIE[$var]) &&  $_COOKIE[$var] == $value) {
					return true;
				}
				return false;

			case 'ENV':
				if (isset($_ENV[$var]) &&  $_ENV[$var] == $value) {
					return true;
				}
				return false;

			case 'FILES':
				if (isset($_FILES[$var]) &&  $_FILES[$var] == $value) {
					return true;
				}
				return false;

			case 'GET':
				if (isset($_GET[$var]) &&  $_GET[$var] == $value) {
					return true;
				}
				return false;

			case 'POST':
				if (isset($_POST[$var]) &&  $_POST[$var] == $value) {
					return true;
				}
				return false;

			case 'REQUEST':
				if (isset($_REQUEST[$var]) &&  $_REQUEST[$var] == $value) {
					return true;
				}
				return false;

			case 'SERVER':
				if (isset($_SERVER[$var]) &&  $_SERVER[$var] == $value) {
					return true;
				}
				return false;

			case 'SESSION':
				if (isset($_SESSION[$var]) &&  $_SESSION[$var] == $value) {
					return true;
				}
				return false;
		}
	}

	#
	#	Supports: COOKIE, ENV, GET, POST, SERVER
	#	Doesn't: FILES, REQUEST, SESSION
	#

	public static function input($glob, $name, $type)
	{
		$glob = strtoupper($glob);

		if (self::isg($glob, $name)) {

			$glob = 'INPUT_' . $glob;

			if ($type == "int") {
				return intval(filter_input(constant($glob), $name, FILTER_SANITIZE_NUMBER_INT));
			}

			if ($type == "float") {
				return filter_input(constant($glob), $name, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}

			if ($type = "str" || $type = "string") {
				return filter_input(constant($glob), $name, FILTER_UNSAFE_RAW);
			}

			if ($type = "email") {
				return filter_input(constant($glob), $name, FILTER_SANITIZE_EMAIL);
			}

			if ($type = "url") {
				return filter_input(constant($glob), $name, FILTER_SANITIZE_URL);
			}

			if ($type = "raw") {
				return filter_input(constant($glob), $name, FILTER_UNSAFE_RAW);
			}
		}
		return false;
	}

	#
	#
	#	STRING
	#
	#

	public static function mb_ucfirst($string, $encoding = "UTF-8")
	{
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}

	public static function isJson($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public static function transliterate($textcyr = NULL, $textlat = NULL)
	{
		$cyr = array(
			'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',
			'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я'
		);
		$lat = array(
			'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q',
			'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q'
		);
		if ($textcyr) return str_replace($cyr, $lat, $textcyr);
		else if ($textlat) return str_replace($lat, $cyr, $textlat);
		else return null;
	}

	public function strpos_recursive($haystack, $needle, $offset = 0, &$results = array())
	{
		$offset = strpos($haystack, $needle, $offset);
		if ($offset === false) {
			return $results;
		} else {
			$results[] = $offset;
			return self::strpos_recursive($haystack, $needle, ($offset + 1), $results);
		}
	}

	public static function hash($length)
	{
		// src: https://stackoverflow.com/questions/4356289/php-random-string-generator
		return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
	}

	#
	#
	#	ARRAY
	#
	#

	#
	# 	Checks if array has a key [with certain value]
	#

	public static function isake($arr, $key, $str = NULL)
	{
		if (isset($arr[$key])) {
			if ($str) {
				if ($key == $str) {
					return true;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	#
	#	Flags each element of $collection
	#

	public static function mark($arr, $index, $value = 1)
	{
		foreach ($arr as &$item) {
			$item[$index] = $value;
		}

		return $arr;
	}

	#
	#	Makes flagged $collection[$property] with $value
	#

	public static function flag($collection, $property, $value, $flag = ['selected' => 'selected'])
	{
		foreach ($collection as $i => &$element) {
			if ($property != "") {
				if ($element[$property] == $value) {
					$element[key($flag)] = current($flag);
				}
			} else {
				$element[key($flag)] = current($flag);
				#Dump::made($element);
			}
		}

		return $collection;
	}

	#
	# 	Returns: 
	# 	- index (from 0 to ...) of element in multi-dimensional array with certain value of a property
	# 	- -1 when not found
	# 	(s=search, m=multi, d=dimensional, a=array)
	# 	
	#	Example:
	# 	$Users = [
	#  		['name' => 'Pete', 'age' => 9],
	#		['name' => 'Kate', 'age' => 23],
	#		['name' => 'John', 'age' => 25],
	#		['name' => 'Kim', 'age' => 38],
	#	];
	#	
	#	$index = Php::smda($Users, 'age', 9); // => 0 (Pete)
	#		
	#	works only when all element has this prop
	#						

	public static function smda($arr, $prop, $val)
	{
		$index = array_search($val, array_column($arr, $prop));
		if ($index === false) {
			$index = -1;
		}

		return $index;
	}

	#
	#	Returns index of element in multi-dimensional array that has max or min value of a property
	#	(s=search, m=multi, d=dimensional, a=array, in m='mode')
	#
	#	Example
	#	$index = Php::smdam($Users, 'age', 'max'); // => 3 (Kim)
	#		

	public static function smdam($arr, $prop, $mode)
	{
		if ($mode == 'max') {
			$val = max(
				array_column($arr, $prop)
			);
		}

		if ($mode == 'min') {
			$val = min(
				array_column($arr, $prop)
			);
		}

		$index = Php::smda($arr, $prop, $val);
		return $index;
	}

	#
	# 	set element with prop=val as first
	# 	(s=set, e=element, f=first, m=multi, d=dimensional, a=array)
	#

	public static function sefmda(&$arr, $prop, $val)
	{

		$i = Php::smda($arr, $prop, $val);
		$el = $arr[$i];
		unset($arr[$i]);
		array_unshift($arr, $el);

		return $arr;
	}

	#
	#	(o=order,m=multi, d=dimensional, a=array)
	#

	public static function oma(&$arr, $col, $dir = SORT_ASC)
	{
		$sort_col = array();
		foreach ($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}

	#
	#	Deletes element with specific value
	#	(a=array: d=delete, e=element, with v=value)
	#	source: https://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
	#

	public static function a_dev(&$arr, $value)
	{
		if (($key = array_search($value, $arr)) !== false) {
			unset($arr[$key]);
		}

		$arr = array_values($arr);
	}

	#
	#	Deletes elements with specific value of key
	#	(m=multi, d=dimensional, a=array: d=delete, e=element)
	#	source: https://stackoverflow.com/questions/4466159/delete-element-from-multidimensional-array-based-on-value
	#

	public static function mda_de($arr, $key, $value)
	{
		foreach ($arr as $k => $e) {
			if ($e[$key] == $value) {
				unset($arr[$k]);
			}
		}
		return $arr;
	}

	#
	#
	#	NUMBERS
	#
	#

	public static function lev($str, $arr)
	{
		// кратчайшее расстояние пока еще не найдено
		$shortest = -1;

		// проходим по словам для нахождения самого близкого варианта
		foreach ($arr as $i => $word) {

			// вычисляем расстояние между входным словом и текущим
			$lev = levenshtein($str, $word);

			// проверяем полное совпадение
			if ($lev == 0) {

				// это ближайшее слово (точное совпадение)
				$closest = $word;
				$shortest = 0;

				// выходим из цикла - мы нашли точное совпадение
				break;
			}

			// если это расстояние меньше следующего наименьшего расстояния
			// ИЛИ если следующее самое короткое слово еще не было найдено
			if ($lev <= $shortest || $shortest < 0) {
				// устанивливаем ближайшее совпадение и кратчайшее расстояние
				$closest  = $word;
				$closestIndex = $i;
				$shortest = $lev;
			}
		}

		return ['index' => $closestIndex, 'word' => $closest, 'distance' => $shortest];

		// echo "Вы ввели: $str\n";
		// if ($shortest == 0) {
		//     echo "Найдено точное совпадение: $closest\n";
		// } else {
		//     echo "Вы не имели в виду: $closest?\n";
		// }
	}

	public static function numRange($num, $step)
	{
		$PlaceIdStr = "$num";
		$PlaceIdStrLength = strlen($PlaceIdStr);

		//$step = $step;
		$stepStr = "$step";
		$stepLength = strlen($stepStr);

		$number = 0;

		if ($PlaceIdStrLength < $stepLength + 1) {
			$number = $step;
		} else {
			$subLength = $PlaceIdStrLength - $stepLength;
			$number = substr($PlaceIdStr, 0, $subLength) . "$step";
			$number = $number * 1;
		}

		return $number;
	}

	#
	#
	#	DATES
	#
	#

	public static function getNowTime()
	{
		$current_timestamp_fndate = date("U");
		# 10-03-2019 12:11:32
		//$date_from_timestamp = date("d-m-Y H:i:s",$current_timestamp_fndate);
		# 12:11:32
		$date_from_timestamp = date("H:i:s.u", $current_timestamp_fndate);
		return $date_from_timestamp;
	}

	#
	#
	#	FILES
	#
	#

	public static function compressImage($source, $destination, $quality)
	{
		$info = getimagesize($source);

		if ($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg($source);

		elseif ($info['mime'] == 'image/gif')
			$image = imagecreatefromgif($source);

		elseif ($info['mime'] == 'image/png')
			$image = imagecreatefrompng($source);

		imagejpeg($image, $destination, $quality);
	}

	public static function curl($url, $post = NULL)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
		if ($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}


	#
	#
	#	MYSQL
	#
	#

	public static function mysqlDatetimeToHtml($mysqlStr)
	{
		$mysqlStr[10] = "T";
		return $mysqlStr;
	}

	public static function mysqlHtmlToDatetime($htmlStr)
	{
		$htmlStr[10] = " ";
		return $htmlStr;
	}
}
