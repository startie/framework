<?php

namespace Startie;

use Startie\View;
use Startie\Url;
use Startie\Php;
use Startie\Route;

class Router
{
	public static Route $route;
	public static float $initializedAt;

	// TODO: test
	public static function routes()
	{
		$routes = [];

		$ConfigPath = App::path("backend/Config/Router/Routs.php");

		/* If there is no config – try to guess */

		if (!file_exists($ConfigPath)) {
			$RoutsPath = App::path("backend/Routs/");

			$RoutsFiles = scandir($RoutsPath);
			foreach ($RoutsFiles as $RoutsFile) {
				if ($RoutsFile !== "." && $RoutsFile !== "..") {
					$RouteContent = require App::path(
						"backend/Routs/{$RoutsFile}"
					);
					$routes = array_merge($RouteContent, $routes);
				}
			}
		}

		/* If there is a config – use it */

		if (file_exists($ConfigPath)) {
			$Config = require $ConfigPath;
			if ($Config == 1) {
				throw new \Startie\Exception(
					"File $ConfigPath should return an array"
				);
			} else if (is_array($Config)) {
				foreach ($Config as $RouteName) {
					$path = App::path("backend/Routs/$RouteName.php");
					$RouteContent = require $path;
					$routes = array_merge($RouteContent, $routes);
				}
			}
		}

		return $routes;
	}

	// TODO: test
	public static function init()
	{
		/* On service */

		if (!$_ENV['POWER']) {
			die('On serivce');
		}

		/* Dev load count (start) */

		Router::$initializedAt = microtime(true);

		/* Find current */

		$find = self::find();
		[
			'isFinded' => $isFinded,
			'findedRouteConfig' => $findedRouteConfig,
			'controllerParams' => $controllerParams
		] = $find;

		/* Render */

		if ($isFinded) {
			Router::render($findedRouteConfig, $controllerParams);
		} else {
			Router::errorPage(404);
			throw new \Startie\Exception("Route for this URL is not found");
		}
	}

	// TODO: test
	public static function find()
	{
		#
		# 	Vars
		# 	

		$isFinded = 0;
		$findedRouteConfig = [];
		$controllerParams = [];

		/* Export all routes */

		$routes = Router::routes();

		#
		# 	1. Parse url
		# 

		# Get 'url' param from apache
		$url = Php::input('GET', 'url', 'str');

		# Get 'url' param from server request uri
		if (!$url) {
			if ($_SERVER['REQUEST_URI'] === "/") {
				$url = "/";
			} elseif (str_starts_with($_SERVER['REQUEST_URI'], "/index.php")) {
				header("Location: /");
			} else {
				if (strpos($_SERVER['REQUEST_URI'], '?') != false) {
					$questionMarkPos = strpos($_SERVER['REQUEST_URI'], '?');
					$url = mb_substr(
						$_SERVER['REQUEST_URI'],
						1,
						$questionMarkPos - 1
					);
				} else {
					$url = mb_substr(
						$_SERVER['REQUEST_URI'],
						1
					);
				}

				//$url = str_replace($url, $_ENV['DOMAIN'], ""); #wtf

				if (!$url) {
					$url = "/";
				}
			}

			if (!$url) {
				throw new \Startie\Exception("
					The 'url' param for Router is not found. Is server running?
				");
			}
		}

		# parse from url
		$urlParts = Router::getPathParts($url);
		$urlPartsCount = count($urlParts);

		#
		# 	2. Parse routes		
		# 

		$routsParsed = [];

		#
		#	Set up routes
		#

		foreach ($routes as $url => $config) {
			$routsParsedUrlParts = Router::getPathParts($url);
			#Dump::make($routsParsedUrlParts);

			if (!isset($config['middles'])) {
				$config['middles'] = "";
			};

			$routsParsed[] = [
				'url' => $url,
				'urlParts' => $routsParsedUrlParts,
				'urlPartsCount' => count($routsParsedUrlParts),
				'controller' => $config['controller'] ?? NULL,
				'type' => $config['type'] ?? NULL,
				'middles' => $config['middles'] ?? NULL,
				'title' => $config['title'] ?? NULL,
				'layout' => $config['layout'] ?? NULL,
			];
		}

		#
		#	3. Mapping
		#

		foreach ($routsParsed as $routeParsed) {
			# If route config is not found yet
			if (!$isFinded) {

				# 	3.1. If count parts is the same
				if ($urlPartsCount == $routeParsed['urlPartsCount']) {

					# 3.1.1. Create var for saving successful checks
					$successfulChecks = 0;

					# 3.1.2. Check part by part in loop
					for ($i = 0; $i < $urlPartsCount; $i++) {

						### echo "Comparing '" . $urlParts[$i] . "' with '" . $routeParsed['urlParts'][$i] . "' ...";

						# A. If part of url has variable or type hint
						if (strpos($routeParsed['urlParts'][$i], '$') !== false) {
							# Get pieces of part for extracting 'variable name' and 'type'
							$routePartPieces = explode(":", $routeParsed['urlParts'][$i]);

							# Here is 'variable name'
							$routePartVar = $routePartPieces[0];
							$routePartVar = str_replace("$", "", $routePartVar);

							# Here is 'type' to validate
							$routePartType = $routePartPieces[1];

							# Check if is it numeric
							if (
								$routePartType == 'int'
								|| $routePartType == 'integer'
								|| $routePartType == 'number'
							) {
								$routePartType = 'numeric';
							}

							# Form validation procedure
							$routePartValidateClassMethodExpression
								= "Startie\Validate::" . $routePartType;

							# Validate url part by "Validate::$routePartType()"
							$isValid = call_user_func_array(
								$routePartValidateClassMethodExpression,
								[
									$urlParts[$i]
								]
							);

							if ($isValid) {
								$successfulChecks++;
								# Forming params of controller (=> $data)
								$controllerParams[$routePartVar] = $urlParts[$i];
							}
						}

						# B. If part of url is just a string
						else {
							if ($urlParts[$i] == $routeParsed['urlParts'][$i]) {
								$successfulChecks++;
							}
						}

						### echo " and it is " . $successfulChecks . "<br>";
					}

					# 3.1.3. Check count of successfull checks with parts count
					if ($successfulChecks == $urlPartsCount) {
						$findedRouteConfig = $routeParsed;
						$isFinded = 1;
						#Dump::made($controllerParams);
					} else {
						$controllerParams = [];
					}
				}
			}
		}

		$result = [
			'isFinded' => $isFinded,
			'findedRouteConfig' => $findedRouteConfig,
			'controllerParams' => $controllerParams,
		];

		return $result;
	}

	// TODO: test
	public static function render($RouteConfig, $controllerParams)
	{
		/* Route */

		$route = new Route($RouteConfig);
		Router::$route = $route;

		/* Boostrap */

		self::bootstrap($route);

		/* Middles */

		self::middles($route);

		/* Title */

		if (isset($route->title)) {
			$title = "<title>$route->title</title>";
		}

		/* Load layout */

		if ($route->layout) {
			$layout = Layout::return(
				ucfirst($route->layout)
			);
		}

		/* Content */

		if (!file_exists($route->controllerFilePath)) {
			throw new \Exception(
				"File on '{$route->controllerFilePath}' doesn't exsists"
			);
		} else {
			require $route->controllerFilePath;
		}


		if (!class_exists($route->controllerNamespacedClass)) {
			throw new \Exception(
				"Class '{$route->controllerNamespacedClass}' doesn't exsists"
			);
		}
		if (!method_exists($route->controllerNamespacedClass, $route->method)) {
			throw new \Exception(
				"Controller method '{$route->classMethodExecution}' doesn't exsists"
			);
		}

		/* If we don't have params */

		if (empty($controllerParams)) {
			$controllerClassMethod = "\Controllers\\" . $route->classMethodExecution;
			$content = call_user_func($controllerClassMethod);
		}

		/* If we have params */
		if (!empty($controllerParams)) {
			$content = call_user_func_array(
				"\Controllers\\" . $route->classMethodExecution,
				[$controllerParams]
			);
		}

		/* Fill blocks */

		if ($route->layout) {
			$Config = require App::path("backend/Config/Layout/Common.php");
			$ConfigBlocks = $Config['blocks'];

			$ConfigBlocks['content'] = $content;
			$ConfigBlocks['title'] = $title;

			foreach ($ConfigBlocks as $BlockLabel => $BlockContent) {
				$layout = str_replace(
					"{{{$BlockLabel}}}",
					$BlockContent ?? "",
					$layout
				);
			}
		}

		if ($route->layout) {
			echo $layout;
		} else {
			echo $content;
		}
	}

	// TODO: test
	public static function bootstrap(Route $route)
	{
		if ($route->type) {
			$RouteTypeUCFirst = ucfirst(strtolower($route->type));

			$BootstrapPath = App::path(
				"backend/Config/Bootstrap/$RouteTypeUCFirst.php"
			);
			if (file_exists($BootstrapPath)) {
				require $BootstrapPath;
			} else {
				throw new \Startie\Exception(
					"Path '$BootstrapPath' is missing"
				);
			}
		}
	}

	// TODO: test
	public static function middles(Route $route)
	{
		if ($route->middles) {
			$middlesStr = str_replace(" ", "", $route->middles);
			$middlesArr = explode(',', $middlesStr);
			foreach ($middlesArr as $middle) {;
				$MiddlePath = App::path("backend/Middles/$middle.php");
				if (file_exists($MiddlePath)) {
					require $MiddlePath;
				} else {
					throw new \Startie\Exception(
						"Path '$MiddlePath' is missing"
					);
				}
			}
		};
	}

	/**
	 * Get array of URI's path parts based on slash
	 * @tested
	 * 
	 * TODO: test
	 */
	public static function getPathParts(string $path): array
	{
		$path = rtrim($path, '/');

		$parts = explode('/', $path);

		return $parts;
	}

	/**
	 * Check if passed signature (controller::method) belongs to current url
	 * 
	 * TODO: test
	 */
	public static function isCurrent(string $signature): bool
	{
		return Url::current() == Url::c($signature);
	}

	/**
	 * Pass error code to display a certain view
	 *
	 * @param  int $code
	 * @return void
	 * 
	 * TODO: test
	 */
	public static function errorPage($code)
	{
		$ConfigRouterPagesPath = App::path("backend/Config/Router/Pages.php");

		if (file_exists($ConfigRouterPagesPath)) {
			$ConfigRouterPages = require App::path(
				"backend/Config/Router/Pages.php"
			);

			if (isset($ConfigRouterPages[$code])) {
				if (isset($ConfigRouterPages[$code]['view'])) {
					$view = $ConfigRouterPages[$code]['view'];
				} else {
					throw new \Startie\Exception(
						"View for '$code' error not found"
					);
				}
			} else {
				throw new \Startie\Exception(
					"Configuration for '$code' not found"
				);
			}

			echo View::r($view);
		} else {
			throw new \Startie\Exception(
				"Error page for code '$code' is not cofigured in 'backend/Config/Router/Pages.php'"
			);
		}
	}
}