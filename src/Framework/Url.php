<?php

namespace Startie;

class Url
{
	use Bootable;

	public static string $ROOT;

	public static function boot(): void
	{
		Url::$isBooted = true;
		Url::defineConstants();
	}

	public static function defineConstants(): void
	{
		// Load

		// • protocol

		if (isset($_ENV['PROTOCOL'])) {
			$protocol = $_ENV['PROTOCOL'];
		} else {
			throw new Exception("Enviroment 'PROTOCOL' is missing");
		}

		// • server: name + port

		if (isset($_ENV['SERVERNAME'])) {
			$servername = $_ENV['SERVERNAME'];
		} else {
			throw new Exception("Enviroment 'SERVERNAME' is missing");
		}

		if (isset($_ENV['SERVERPORT'])) {
			$serverport = $_ENV['SERVERPORT'];
		} else {
			throw new Exception("Enviroment 'SERVERPORT' is missing");
		}

		$server = $servername . $serverport;

		// • domain

		if (isset($_ENV['DOMAIN'])) {
			$domain = $_ENV['DOMAIN'];
		} else {
			throw new Exception("Enviroment 'DOMAIN' is missing");
		}

		// Constants
		// define("APP_PROTOCOL", $protocol);
		// define("URL_APP", $protocol . $server . $domain);
		// Url::$ROOT = URL_APP;
		// define("PUBLIC_URL", URL_APP . "public/");
		// define("STORAGE_URL", URL_APP . "storage/");

		App::$APP_PROTOCOL = $protocol;
		App::$URL_APP = $protocol . $server . $domain;
		Url::$ROOT = App::$URL_APP;
		App::$PUBLIC_URL = App::$URL_APP . "public/";
		App::$STORAGE_URL = App::$URL_APP . "storage/";
		App::$NODE_MODULES_URL = App::$URL_APP . "node_modules/";
	}

	/**
	 * Generate app url
	 *
	 * @param  string $path
	 * @param  null|array $queryParams
	 * @param  null|array $fragmentParams 
	 * @return string
	 */
	public static function app(
		string $path = "",
		array|null $queryParams = null,
		array|null $fragmentParams = null,
		bool $arraishQueryParams = false
	): string {
		Url::requireBoot();

		// Fix of double slashes like:
		// http://domain.test//?q=go
		if ($path === "/") $path = "";

		$url = Url::$ROOT . $path;

		$query = "";
		$query = Url::buildParams($queryParams, $arraishQueryParams);
		if ($query !== "") {
			$url .= "?" . $query;
		}

		$fragment = "";
		$fragment = Url::buildParams($fragmentParams, $arraishQueryParams);
		if ($fragment !== "") {
			$url .= "#" . $fragment;
		}

		/* Fix #1: ?& */

		$url = str_replace("?&", "?", $url);

		/* Fix #2: double trailing slash */

		// If penultimate and last symbols are slashes
		if ($url[strlen($url) - 2] === "/" && $url[strlen($url) - 1] === "/") {
			// Then cut off the last slash
			$url = substr($url, 0, strlen($url) - 1);
		}

		/* Fix #3: delete trailin ? */
		if (strpos($url, "?") == strlen($url) - 1) {
			$url = substr($url, 0, strlen($url) - 1);
		}

		return $url;
	}

	/**
	 * @psalm-suppress RedundantCondition
	 * @param bool $arraish If params are ['id' => [1,2]]
	 * When `true`  we will get id[]=1&id[]=2
	 * When `false` we will get id=1&id=2
	 */
	public static function buildParams(
		array|null $params = null,
		bool $arraish = false
	): string {
		$result = "";

		/* Filter empty */

		$paramsFiltered = [];

		if ($params !== null && $params !== []) {
			foreach ($params as $i => $param) {
				if ($param != "") {
					$paramsFiltered[$i] = $param;
				}
			}
			$params = $paramsFiltered;
		}

		/* Build params */

		if ($params !== [] && is_array($params)) {
			// 1. Parse params as array
			$paramsThatAreArrays = [];
			foreach ($params as $i => &$param) {
				if (is_array($param)) {
					$paramsThatAreArrays[$i] = $param;
					unset($params[$i]);
				}
			}

			if (!empty($paramsThatAreArrays)) {
				$result = http_build_query($params);
				foreach ($paramsThatAreArrays as $paramName => $paramValue) {
					if (is_array($paramValue)) {
						foreach ($paramValue as $paramVal) {
							$result .= "&" . $paramName;
							if ($arraish) {
								$result .= "[]";
							}
							$result .= "=" . $paramVal;
						}
					} else {
						$result .= "&" . $paramName . "=" . $paramValue;
					}
				}
			}

			// 2. Parse params as simple string
			else {
				$result = http_build_query($params);
			}
		}

		return $result;
	}

	/**
	 * Url from controller expression
	 */
	public static function controller(
		string $routeExpression,
		array|null $ControllerParams = null,
		array|null $queryParams = null,
		bool $arraishQueryParams = false
	): string {
		$foundedUrl = "";

		$routes = Router::getRoutes();

		// Find route config
		foreach ($routes as $routeUrl => $routeData) {
			if ($routeData['controller'] == $routeExpression) {
				$foundedUrl = $routeUrl;
			}
		}

		// If found
		if ($foundedUrl !== "") {
			// Find vars of route
			$findedVars = [];
			preg_match_all('/\$[a-zA-Z]*/', $foundedUrl, $findedVars);

			$foundedUrl = str_replace("$", "", $foundedUrl);
			$foundedUrl = preg_replace("/:[a-zA-Z]*/", "", $foundedUrl);

			foreach ($findedVars[0] as $findedVar) {
				$findedVar = str_replace("$", "", $findedVar);

				// Form url by replacing matches
				$varValue = $ControllerParams[$findedVar] ?? "";
				if ($varValue !== "") {
					$foundedUrl = str_replace(
						$findedVar,
						$varValue,
						$foundedUrl
					);
				}
			}

			if (is_array($foundedUrl)) {
				throw new Exception("Url cannot by an array");
			}

			$uri = Url::app(
				$foundedUrl,
				$queryParams,
				null,
				$arraishQueryParams
			);

			return $uri;
		}

		// If not
		else {
			throw new \Exception("Can't find a route for '$routeExpression'");
		}
	}

	/**
	 * Shortcut alias for method controller()
	 */
	public static function c(
		string $routeExpression,
		array|null $controllerParams = null,
		array|null $queryParams = null,
		bool $arraishQueryParams = false
	): string {
		return Url::controller(
			$routeExpression,
			$controllerParams,
			$queryParams,
			$arraishQueryParams
		);
	}

	/**
	 * Gives a URL of current page
	 * 
	 * Basically string will be exactly the same as what user sees in the browser.
	 *
	 * @return string
	 */
	public static function current()
	{
		Url::requireBoot();

		$url = App::$APP_PROTOCOL
			. ($_SERVER['HTTP_HOST'] ?? "")
			. ($_SERVER['REQUEST_URI'] ?? "");

		return $url;
	}

	/**
	 * 
	 * Get query params as an array.
	 * 
	 * Gives a structured represantation of the current query string 
	 * or values of certain param.
	 *
	 * @param string|null $param If it is presented returns only that values 
	 * that belongs to a certain param.
	 * @param string|null $queryString Сan be query string or the url.
	 * @param bool $decoded To decode or not each value of pair.
	 * @return array
	 */
	public static function getQueryParams(
		string|null $param = null,
		string|null $queryString = null,
		bool $decoded = false,
		string $part = "query"
	): array {

		// 1. Query string
		// a) Use current query string 
		if ($queryString === null) {
			$queryString = $_SERVER['QUERY_STRING'] ?? '';
		}

		// b) Use passed
		else {
			// If there is a question mark ...
			if (strpos($queryString, "?") !== false) {
				// ... percieve it as a full url
				$url = $queryString;
				// ... and delete part before '?'
				$urlParsedArr = parse_url($url);

				if (isset($urlParsedArr[$part])) {
					$urlParsedPart = $urlParsedArr[$part];
				} else {
					throw new \Startie\Exception(
						"Part '$part' is missing in the '$url'"
					);
				}

				if ($urlParsedPart) {
					$queryString = $urlParsedPart;
				} else {
					$queryString = "";
				}
			}
		}

		if ($queryString === "") {
			return [];
		}

		$queryString = (string) $queryString;

		// 2. Set up
		// Initial variables
		$parts = [];
		$pairs = [];
		$result = [];

		// 3. Explode
		// Explode query string
		$parts = explode('&', $queryString);

		// Make an array of parts
		foreach ($parts as $pair) {
			$pairArr = explode('=', $pair);
			$pairs[] = $pairArr;
		}

		// 4. Build the final array
		foreach ($pairs as $pairArr) {
			$name = $pairArr[0];
			if ($decoded) {
				$value = urldecode($pairArr[1]);
			} else {
				$value = $pairArr[1] ?? "";
			}

			foreach ($pairArr as $key) {
				// Take only unque values
				if (!array_key_exists($name, $result)) {
					// Make array with its name
					$result[$name] = [];
				}
			}
			// Add to their array matched values
			$result[$name][] = $value;
		}

		// 5. Return
		// Return either all pairs for all params or only for the exact one

		if ($param === "null") {
			return $result;
		} else {
			if (isset($result[$param])) {
				return $result[$param];
			}
		}

		return [];
	}

	/**
	 * Get query or ...
	 *
	 * @deprecated No longer used. Use getQueryParams()
	 */
	public static function paramsArr(
		string $url,
		string $mode = 'fragment'
	): array {
		$urlPartsString = parse_url($url);

		if (isset($urlPartsString[$mode])) {
			$urlPartsString = (string) $urlPartsString[$mode];
			$urlPartsArr = explode("&", $urlPartsString);
			$urlParamsArr = [];
			foreach ($urlPartsArr as $urlPart) {
				$urlPartExplode = explode("=", $urlPart);
				$urlParamsArr[$urlPartExplode[0]] = $urlPartExplode[1];
			}

			return $urlParamsArr;
		} else {
			return [];
		}
	}

	/**
	 * Gets title on the url
	 * 
	 * Utility method.
	 * @deprecated Will be moved to another class, because it is mostly a parser
	 */
	public static function getTitle(string $url): string
	{
		$str = file_get_contents($url);

		if (strlen($str) > 0) {
			$str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>

			$str = htmlentities($str);

			preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case

			$str = $title[1] ?? "";
			$str = htmlentities($str);
		}
		return $str;
	}

	/**
	 * Finalizes url through redirects
	 * @deprecated Will be moved to another class, because it is mostly a parser
	 */
	public static function finalize(string $url, int $maxRequests = 10): string
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRequests);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Link Checker)');

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);

		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		curl_close($ch);
		return $url;
	}

	public static function cleanFromQueryString(string $path): string
	{
		$questionMarkPosition = mb_strpos($path, '?');

		if ($questionMarkPosition !== false) {
			$path = mb_substr($path, 1, $questionMarkPosition - 1);
		}

		return $path;
	}
}