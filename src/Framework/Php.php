<?php

namespace Startie;

class Php
{
	#
	#
	#	COMMON
	#
	#

	/**
	 * Checks if variable is set and has a certain value
	 */
	public static function isve(mixed $var, mixed $val = null): bool
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

	/**
	 * Checks if two variables has smth in common
	 * TODO: support for double, float
	 */

	public static function hasInCommon(mixed $one, mixed $two): bool
	{
		// Array
		if (is_array($one) && is_array($two)) {
			$intersect = array_intersect_key($one, $two);
			if (!empty($intersect)) {
				return true;
			} else {
				return false;
			}
		}

		// Bool
		else if (is_bool($one) && is_bool($two)) {
			if ($one == $two) {
				return true;
			} else {
				return false;
			}
		}

		// Int, float
		else if (is_numeric($one) && is_numeric($two)) {
			if ($one == $two) {
				return true;
			} else {
				return false;
			}
		}

		// Null
		else if (is_null($one) && is_null($two)) {
			return true;
		}

		// String
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

	/**
	 * @source https://stackoverflow.com/questions/5225971/is-it-possible-to-get-list-of-defined-namespaces
	 */
	public static function namespaceExists(string $namespace): bool
	{
		$namespace .= "\\";
		foreach (get_declared_classes() as $name) {
			if (strpos($name, $namespace) === 0) return true;
		}

		return false;
	}

	#
	#
	#	GLOBALS
	#
	#

	/**
	 * Check if the superglobal variable has a certain variable
	 */
	public static function isg(
		string $superGlobalVariableName,
		string $needleName
	): bool {
		$superGlobalVariableName = strtoupper($superGlobalVariableName);

		switch ($superGlobalVariableName) {
			case 'COOKIE':
				if (isset($_COOKIE[$needleName])) {
					return true;
				}
				return false;

			case 'ENV':
				if (isset($_ENV[$needleName])) {
					return true;
				}
				return false;

			case 'FILES':
				if (isset($_FILES[$needleName])) {
					return true;
				}
				return false;

			case 'GET':
				if (isset($_GET[$needleName])) {
					return true;
				}
				return false;

			case 'POST':
				if (isset($_POST[$needleName])) {
					return true;
				}
				return false;

			case 'REQUEST':
				if (isset($_REQUEST[$needleName])) {
					return true;
				}
				return false;

			case 'SERVER':
				if (isset($_SERVER[$needleName])) {
					return true;
				}
				return false;

			case 'SESSION':
				if (isset($_SESSION[$needleName])) {
					return true;
				}
				return false;
			default:
				return false;
		}
	}

	/**
	 * Checks if (super) global has a variable with ceratain value is exists
	 */
	public static function isgve(mixed $glob, string $var, mixed $value): bool
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
			default:
				return false;
		}
	}

	/**
	 * Supports: COOKIE, ENV, GET, POST, SERVER
	 * Does not support: FILES, REQUEST, SESSION
	 * 
	 * Wrapper around `filter_input()`
	 * @psalm-suppress all
	 * @deprecated Since 0.46.0
	 */
	public static function input(
		string $glob,
		string $variableName,
		string $type
	): mixed {
		$glob = strtoupper($glob);

		$superGlobalHasVariable = Php::isg($glob, $variableName);

		if ($superGlobalHasVariable) {
			$typeConstName = 'INPUT_' . $glob;
			$typeConstValue = constant($typeConstName);

			$type = strtolower($type);

			if ($type === "int") {
				// Deleted `intval` here, since false will be interpreted as 0
				// return intval(filter_input(
				return filter_input(
					$typeConstValue,
					$variableName,
					FILTER_SANITIZE_NUMBER_INT
				);
				// ));
			}

			if ($type === "float") {
				return filter_input(
					$typeConstValue,
					$variableName,
					FILTER_SANITIZE_NUMBER_FLOAT,
					FILTER_FLAG_ALLOW_FRACTION
				);
			}

			if ($type === "str" || $type === "string") {
				return filter_input(
					$typeConstValue,
					$variableName,
					FILTER_UNSAFE_RAW
				);
			}

			if ($type === "email") {
				return filter_input(
					$typeConstValue,
					$variableName,
					FILTER_SANITIZE_EMAIL
				);
			}

			if ($type === "url") {
				return filter_input(
					$typeConstValue,
					$variableName,
					FILTER_SANITIZE_URL
				);
			}

			if ($type === "raw") {
				return filter_input(
					$typeConstValue,
					$variableName,
					FILTER_UNSAFE_RAW
				);
			}
		}

		return false;
	}

	#
	#
	#	STRING
	#
	#

	public static function mb_ucfirst(
		string $string,
		string $encoding = "UTF-8"
	): string {
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);

		return mb_strtoupper($firstChar, $encoding) . $then;
	}

	/**
	 * @source https://gist.github.com/MakStashkevich/31f5cb7b229bc735aeaa89a6796327ce
	 */
	public static function mb_str_pad(
		string $input,
		int $pad_length,
		string $pad_string = "\x20",
		int $pad_type = STR_PAD_RIGHT,
		string $encoding = 'UTF-8'
	): string {
		$input_length = mb_strlen($input, $encoding);
		$pad_string_length = mb_strlen($pad_string, $encoding);

		if ($pad_length <= 0 || ($pad_length - $input_length) <= 0) {
			return $input;
		}

		$num_pad_chars = $pad_length - $input_length;

		switch ($pad_type) {
			case STR_PAD_RIGHT:
				$left_pad = 0;
				$right_pad = $num_pad_chars;
				break;

			case STR_PAD_LEFT:
				$left_pad = $num_pad_chars;
				$right_pad = 0;
				break;

			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
		}

		if (!isset($left_pad) || !isset($right_pad)) {
			throw new \Exception('The left pad or the right pad are undefined');
		}

		$result = '';
		for ($i = 0; $i < $left_pad; ++$i) {
			$result .= mb_substr($pad_string, $i % $pad_string_length, 1, $encoding);
		}
		$result .= $input;
		for ($i = 0; $i < $right_pad; ++$i) {
			$result .= mb_substr($pad_string, $i % $pad_string_length, 1, $encoding);
		}

		return $result;
	}

	public static function isJson(string|null $string): bool
	{
		if (isset($string)) {
			json_decode($string);

			return (json_last_error() == JSON_ERROR_NONE);
		} else {
			return false;
		}
	}

	public static function transliterate(
		string|null $textcyr = null,
		string|null $textlat = null
	): string|null {
		$cyr = array(
			'ж',
			'ч',
			'щ',
			'ш',
			'ю',
			'а',
			'б',
			'в',
			'г',
			'д',
			'е',
			'з',
			'и',
			'й',
			'к',
			'л',
			'м',
			'н',
			'о',
			'п',
			'р',
			'с',
			'т',
			'у',
			'ф',
			'х',
			'ц',
			'ъ',
			'ь',
			'я',
			'Ж',
			'Ч',
			'Щ',
			'Ш',
			'Ю',
			'А',
			'Б',
			'В',
			'Г',
			'Д',
			'Е',
			'З',
			'И',
			'Й',
			'К',
			'Л',
			'М',
			'Н',
			'О',
			'П',
			'Р',
			'С',
			'Т',
			'У',
			'Ф',
			'Х',
			'Ц',
			'Ъ',
			'Ь',
			'Я'
		);

		$lat = array(
			'zh',
			'ch',
			'sht',
			'sh',
			'yu',
			'a',
			'b',
			'v',
			'g',
			'd',
			'e',
			'z',
			'i',
			'j',
			'k',
			'l',
			'm',
			'n',
			'o',
			'p',
			'r',
			's',
			't',
			'u',
			'f',
			'h',
			'c',
			'y',
			'x',
			'q',
			'Zh',
			'Ch',
			'Sht',
			'Sh',
			'Yu',
			'A',
			'B',
			'V',
			'G',
			'D',
			'E',
			'Z',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'R',
			'S',
			'T',
			'U',
			'F',
			'H',
			'c',
			'Y',
			'X',
			'Q'
		);

		if ($textcyr !== null && $textcyr !== "") {
			return str_replace($cyr, $lat, $textcyr);
		} elseif ($textlat !== null && $textlat !== "") {
			return str_replace($lat, $cyr, $textlat);
		} else {
			return null;
		}
	}

	public function strpos_recursive(
		string $haystack,
		string $needle,
		int $offset = 0,
		array &$results = []
	): array {
		$offset = strpos($haystack, $needle, $offset);

		if ($offset === false) {
			return $results;
		} else {
			$results[] = $offset;
			return self::strpos_recursive(
				$haystack,
				$needle,
				($offset + 1),
				$results
			);
		}
	}

	/**
	 * @source https://stackoverflow.com/questions/4356289/php-random-string-generator
	 */
	public static function hash(int $length): string
	{
		return substr(
			str_shuffle(
				str_repeat(
					$x = '0123456789'
						. 'abcdefghijklmnopqrstuvwxyz'
						. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',

					(int) ceil($length / strlen($x))
				)
			),
			1,
			$length
		);
	}

	#
	#
	#	ARRAY
	#
	#

	/**
	 * Checks if array has a key [with certain value]
	 */
	public static function isake(
		array $arr,
		string $key,
		string|null $str = null
	): bool {
		if (isset($arr[$key])) {
			if ($str !== null) {
				if ($key == $str) {
					return true;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	/**
	 * Flags each element of $collection
	 */
	public static function mark(
		array $arr,
		int|string $index,
		int $value = 1
	): array {
		foreach ($arr as &$item) {
			$item[$index] = $value;
		}

		return $arr;
	}

	/**
	 * Makes flagged $collection[$property] with $value
	 */
	public static function flag(
		array $collection,
		string|int $property,
		mixed $value,
		array $flag = ['selected' => 'selected']
	): array {
		foreach ($collection as $i => &$element) {
			if ($property != "") {
				if ($element[$property] == $value) {
					$element[key($flag)] = current($flag);
				}
			} else {
				$element[key($flag)] = current($flag);
			}
		}

		return $collection;
	}

	/**
	 * Returns: 
	 * - index (from 0 to ...) of element in multi-dimensional array with certain value of a property
	 * - -1 when not found
	 * (s=search, m=multi, d=dimensional, a=array)
	 * 	# 	
	 * Example:
	 * $Users = [
	 * 	['name' => 'Pete', 'age' => 9],
	 * 	['name' => 'Kate', 'age' => 23],
	 * 	['name' => 'John', 'age' => 25],
	 * 	['name' => 'Kim', 'age' => 38],
	 * ];
	 * 	#	
	 * $index = Php::smda($Users, 'age', 9); // => 0 (Pete)
	 * 	
	 * works only when all element has this prop
	 */
	public static function smda(
		array $arr,
		string|int $prop,
		mixed $val
	): int {
		$index = array_search($val, array_column($arr, $prop));

		if ($index === false) {
			$index = -1;
		}

		return $index;
	}

	/**
	 * Returns index of element in multi-dimensional array that has 
	 * max or min value of a property
	 * (s=search, m=multi, d=dimensional, a=array, in m='mode')
	 * Example
	 * $index = Php::smdam($Users, 'age', 'max'); // => 3 (Kim)
	 */
	public static function smdam(
		array $arr,
		string|int $prop,
		string $mode
	): int {
		$allValues = array_column($arr, $prop);

		if ($allValues === []) {
			throw new Exception('No values in array for this prop');
		}

		if ($mode == 'max') {	
			$val = max($allValues);
		} elseif ($mode == 'min') {
			$val = min($allValues);
		} else {
			throw new \Exception('Unknown mode');
		}

		if (!isset($val)) {
			return -1;
		}

		$index = Php::smda($arr, $prop, $val);

		return $index;
	}

	/**
	 * Sets element with prop=val as first
	 * (s=set, e=element, f=first, m=multi, d=dimensional, a=array)
	 */
	public static function sefmda(
		array &$arr,
		string|int $prop,
		mixed $val
	): array {

		$i = Php::smda($arr, $prop, $val);
		$el = $arr[$i];
		unset($arr[$i]);
		array_unshift($arr, $el);

		return $arr;
	}

	/**
	 * (o=order,m=multi, d=dimensional, a=array)
	 */
	public static function oma(
		array &$arr,
		string|int $col,
		int $dir = SORT_ASC
	): void {
		$sort_col = [];
		foreach ($arr as $key => $row) {
			if (isset($row[$col])) {
				$sort_col[$key] = $row[$col];
			}
		}
		array_multisort($sort_col, $dir, $arr);
	}

	/**
	 * Deletes element with specific value
	 * (a=array: d=delete, e=element, with v=value)
	 * @source https://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
	 */
	public static function a_dev(
		array &$arr,
		mixed $value
	): void {
		if (($key = array_search($value, $arr)) !== false) {
			unset($arr[$key]);
		}

		$arr = array_values($arr);
	}

	/**
	 * Deletes elements with specific value of key
	 * (m=multi, d=dimensional, a=array: d=delete, e=element)
	 * @source https://stackoverflow.com/questions/4466159/delete-element-from-multidimensional-array-based-on-value
	 */
	public static function mda_de(array $arr, int|string $key, mixed $value): array
	{
		foreach ($arr as $k => $e) {
			if (isset($e[$key])) {
				if ($e[$key] == $value) {
					unset($arr[$k]);
				}
			}
		}
		return $arr;
	}

	#
	#
	#	NUMBERS
	#
	#

	public static function lev(string $str, array $arr): array
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

		return [
			'index' => $closestIndex ?? null,
			'word' => $closest ?? null,
			'distance' => $shortest
		];

		// echo "Вы ввели: $str\n";
		// if ($shortest == 0) {
		//     echo "Найдено точное совпадение: $closest\n";
		// } else {
		//     echo "Вы не имели в виду: $closest?\n";
		// }
	}

	public static function numRange(string $num, string $step): int
	{
		$PlaceIdStr = "$num";
		$PlaceIdStrLength = strlen($PlaceIdStr);

		$stepStr = "$step";
		$stepLength = strlen($stepStr);

		$number = 0;

		if ($PlaceIdStrLength < $stepLength + 1) {
			$number = $step;
		} else {
			$subLength = $PlaceIdStrLength - $stepLength;
			$number = substr($PlaceIdStr, 0, $subLength) . "$step";
		}

		$number = (int) $number;

		return $number;
	}

	#
	#
	#	DATES
	#
	#

	public static function getNowTime(): string
	{
		$current_timestamp_fndate = (int) date("U");
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

	public static function compressImage(
		string $source,
		string $destination,
		int $quality
	): void {
		$info = getimagesize($source);

		if ($info['mime'] == 'image/jpeg') {
			$image = imagecreatefromjpeg($source);
		} elseif ($info['mime'] == 'image/gif') {
			$image = imagecreatefromgif($source);
		} elseif ($info['mime'] == 'image/png') {
			$image = imagecreatefrompng($source);
		}

		if (isset($image)) {
			if ($image !== false) {
				imagejpeg($image, $destination, $quality);
			}
		}
	}

	public static function curl(string $url, string|array $post = ""): string|bool
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt(
			$ch,
			CURLOPT_USERAGENT,
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3'
		);

		if ($post !== "") {
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

	public static function mysqlDatetimeToHtml(string $mysqlStr): string
	{
		$mysqlStr[10] = "T";
		return $mysqlStr;
	}

	public static function mysqlHtmlToDatetime(string $htmlStr): string
	{
		$htmlStr[10] = " ";
		return $htmlStr;
	}
}