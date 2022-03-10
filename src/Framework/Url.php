<?php

namespace Startie;

class Url
{
	#
	#	Examples:
	#	$params = [
	#		'typeId' => [1, 2],
	#		'q' => 'hello',
	#		'view' => ['list']
	#	];
	#

	public static function app($name = null, $params = null)
	{
		# Filter empty
		$paramsFiltered = [];

		if (!empty($params)) {
			foreach ($params as $i => $param) {
				if ($param != "") {
					$paramsFiltered[$i] = $param;
				}
			}
			$params = $paramsFiltered;
		}

		# Build
		if ($params && is_array($params)) {
			# 1. Parse params as array
			$paramsThatAreArrays = [];
			foreach ($params as $i => &$param) {
				if (is_array($param)) {
					$paramsThatAreArrays[$i] = $param;
					unset($params[$i]);
				}
			}
			if (!empty($paramsThatAreArrays)) {
				$q = http_build_query($params);
				foreach ($paramsThatAreArrays as $paramName => $paramValue) {

					$q .= "&" . $paramName . "=" . implode(",", $paramValue);
				}
			}

			# 2. Parse params as simple string
			else {
				$q = http_build_query($params);
			}

			$url = URL_APP . $name . "?" . $q;
		} else {
			$url = URL_APP . $name;
		}

		#	Clear

		$url = str_replace("?&", "?", $url);

		return $url;
	}

	public static function controller($routeExpression, $controllerParams = null, $getParams = null)
	{
		global $Routs;
		$searchUrl = "";

		# Find route config
		foreach ($Routs as $routeUrl => $routeData) {
			if ($routeData['controller'] == $routeExpression) {
				$searchUrl = $routeUrl;
			}
		}

		# If found
		if ($searchUrl) {
			# Find vars of route
			$findedVars = [];
			preg_match_all('/\$[a-zA-Z]*/', $searchUrl, $findedVars);

			$searchUrl = str_replace("$", "", $searchUrl);
			$searchUrl = preg_replace("/:[a-zA-Z]*/", "", $searchUrl);

			foreach ($findedVars[0] as $findedVar) {
				$findedVar = str_replace("$", "", $findedVar);

				# Form url by replacing matches
				if ($controllerParams[$findedVar]) {
					$searchUrl = str_replace($findedVar, $controllerParams[$findedVar], $searchUrl);
				}
			}

			return static::app($searchUrl, $getParams);
		}


		# If not
		else {
			//die('hello');
			Dump::make("Can't find route for " . $routeExpression);
		}
	}

	public static function c($routeExpression, $controllerParams = null, $getParams = null)
	{
		return Url::controller($routeExpression, $controllerParams, $getParams);
	}

	public static function controllerGetty($routeExpression, $params = null)
	{
		global $Routs;
		$searchUrl = "";

		foreach ($Routs as $routeUrl => $routeData) {
			if ($routeData['controller'] == $routeExpression) {
				$searchUrl = $routeUrl;
			}
		}

		return static::app($searchUrl);
	}

	public static function current()
	{
		//$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$url = APP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return $url;
	}

	public static function getQueryParams($prop = null)
	{
		$explosion = [];
		$temp = [];
		$result = [];

		// Make explosion of query string
		#todo sanitize
		$explosion = explode('&', $_SERVER['QUERY_STRING']);


		// Make array of explosion
		foreach ($explosion as $param) {
			$var = explode('=', $param);
			$temp[] = $var;
		}

		foreach ($temp as $i => $arr) {
			foreach ($arr as $key) {
				// Take only unque values
				if (!array_key_exists($arr[0], $result)) {
					// Make array with its name
					$result[$arr[0]] = [];
				}
			}
			// Add to their array matched values
			$result[$arr[0]][] = $arr[1];
		}

		if (isset($result[$prop])) {
			return $result[$prop];
		} else if (is_null($prop)) {
			return $result;
		}
	}

	public static function getTitle($url)
	{
		$str = file_get_contents($url);

		if (strlen($str) > 0) {
			$str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>

			$str = mb_convert_encoding($str, 'HTML-ENTITIES', "UTF-8");

			preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case

			$str = $title[1];
			$str = mb_convert_encoding($str, 'UTF-8', 'HTML-ENTITIES');

			return $str;
		}

		//  	function file_get_contents_utf8($fn) {
		//      $content = file_get_contents($fn);
		//       return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
		// }

		// $str = file_get_contents_utf8($str);
		//  	$str = mb_convert_encoding($str, 'HTML-ENTITIES', "UTF-8");
		//  	Dump::made($url);
	}

	public static function paramsArr($url)
	{
		$urlPartsString = parse_url($url)['fragment'];
		$urlPartsArr = explode("&", $urlPartsString);
		$urlParamsArr = [];
		foreach ($urlPartsArr as $urlPart) {
			$urlPartExplode = explode("=", $urlPart);
			$urlParamsArr[$urlPartExplode[0]] = $urlPartExplode[1];
		}

		return $urlParamsArr;
	}

	public static function finalize($url, $maxRequests = 10)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRequests);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		//customize user agent if you desire...
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Link Checker)');

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);

		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		curl_close($ch);
		return $url;
	}
}
