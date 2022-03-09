<?php

class Router
{
	public static function routs($Config)
	{
		$backend = dirname(dirname(__DIR__));

		$Routs = [];

		foreach ($Config as $RouteName) 
		{
			$path = "$backend/Routs/$RouteName.php";
			$RouteContent = require $path;
			$Routs = array_merge($RouteContent, $Routs);
		}
		return $Routs;
	}
	
	public static function init()
	{
		if(!$_ENV['POWER']){
			die('On serivce');
		}
		
		$start_time = microtime(true);

		#
		# 	Vars
		# 	
	
		$backend = dirname(dirname(__DIR__));	
		$isFinded = 0;
		$findedRouteConfig;
		$controllerParams = [];

		#
		# 	1. Parse url
		# 
		
		# Get 'url' param
		$url = Php::input('GET', 'url', 'str');
		
		$urlParts = Router::explodeUrl(['url' => $url]);
		$urlPartsCount = count($urlParts);

		#
		# 	2. Parse routs		
		# 
		
		$routsParsed = [];
		
		#
		#	Set up routs
		#
		
		global $Routs;
		$Routs = Router::routs($Routs);
		#Dump::made($Routs);

		foreach ($Routs as $url => $config) {
			$routsParsedUrlParts = Router::explodeUrl(['url' => $url]);
			#Dump::make($routsParsedUrlParts);
			
			if(!isset($config['middles'])) { $config['middles'] = ""; };

			@ $routsParsed[] = [
				'url' => $url, 
				'urlParts' => $routsParsedUrlParts,
				'urlPartsCount' => count($routsParsedUrlParts),
				'controller' => $config['controller'], 
				'type' => $config['type'],
				'middles' => $config['middles'],
				'title' => $config['title'],
				'layout' => $config['layout'],
			];
		}

		#
		#	3. Mapping
		#
		
		foreach ($routsParsed as $routeParsed) 
		{
			# If route config is not found yet
			if(!$isFinded){

				# 	3.1. If count parts is the same
				if($urlPartsCount == $routeParsed['urlPartsCount'])
				{

					# 3.1.1. Create var for saving successful checks
					$successfulChecks = 0;

					# 3.1.2. Check part by part in loop
					for($i = 0; $i < $urlPartsCount; $i++)
					{
						### echo "Comparing '" . $urlParts[$i] . "' with '" . $routeParsed['urlParts'][$i] . "' ...";
						
						# A. If part of url has variable or type hint
						if(strpos($routeParsed['urlParts'][$i], '$') !== false)
						{
							# Get pieces of part for extracting 'variable name' and 'type'
							$routePartPieces = explode(":", $routeParsed['urlParts'][$i]);
						
							# Here is 'variable name'
							$routePartVar = $routePartPieces[0];
							$routePartVar = str_replace("$", "", $routePartVar);

							# Here is 'type' to validate
							$routePartType = $routePartPieces[1];

							# Check if is it numeric
							if($routePartType == 'int' || $routePartType == 'integer' || $routePartType == 'number'){
								$routePartType = 'numeric';
							}

							# Form validation procedure
							$routePartValidateClassMethodExpression = "Validate::" . $routePartType;

							# Validate url part by "Validate::$routePartType()"
							$isValid = call_user_func_array(
								$routePartValidateClassMethodExpression, [ 
									$urlParts[$i]
								]
							);	

							if($isValid){
								$successfulChecks++;
								# Forming params of controller (=> $data)
								$controllerParams[$routePartVar] = $urlParts[$i];
							}
						}

						# B. If part of url is just a string
						else {
							if ($urlParts[$i] == $routeParsed['urlParts'][$i]){
								$successfulChecks++;
							}
						}
						### echo " and it is " . $successfulChecks . "<br>";
					}
					
					# 3.1.3. Check count of successfull checks with parts count
					if($successfulChecks == $urlPartsCount){
						$findedRouteConfig = $routeParsed;
						$isFinded = 1;
						#Dump::made($controllerParams);
					} else {
						$controllerParams = [];
					}
					
				}

			}
		}
		
		#
		#	4. Check result
		#
		
		# 	4.1. If route is finded		
		if($isFinded){
			
			# 4.1.1 Include bootstrap	
			$routeType = ucfirst($findedRouteConfig['type']);
			require "$backend/Config/Bootstrap/$routeType.php";

			# 4.1.2 Include middles
			if($findedRouteConfig['middles']){
				$middlesStr = str_replace(" ", "", $findedRouteConfig['middles']);
				$middlesArr = explode(',', $middlesStr);
				foreach ($middlesArr as $middle) {;
					require "$backend/Middles/$middle.php";
				}
			};

			# 4.1.3 Title for PAGE
			if($routeType == 'Page'){
				if(isset($findedRouteConfig['title'])){
					$routeTitle = $findedRouteConfig['title'];
					View::title($routeTitle);
				}
			}

			# 4.1.4 Layout 'before' part for PAGE
			if($routeType == 'Page'){
				if(isset($findedRouteConfig['layout'])){
					$layoutName = ucfirst($findedRouteConfig['layout']);
					require(BACKEND_DIR . 'Layouts/' . $layoutName . '/Before.php');
				}
			}
			
			# 4.1.5 CSS for PAGE
			if($routeType == 'Page'){
				$controllerClass = explode("::", $findedRouteConfig['controller'])[0];
				//Dump::make($controllerClass);
				$controllerFunction = ucfirst(explode("::", $findedRouteConfig['controller'])[1]);
				$hash = Asseter::getJsHash();
				$filePath = "css/Pages"  . $controllerClass . $controllerFunction . Asseter::$cssPrefix . "." . $hash . ".css";
				$fileDir = PUBLIC_DIR . $filePath;
				if(file_exists($fileDir)){
					echo "<link href='" . PUBLIC_URL . $filePath . "' rel='stylesheet' type='text/css'>";
				} else {
					//Dump::make(PUBLIC_URL . $filePath);
				}
			}

			# 4.1.6 Extracting execution expression

			# Form possible execution expression
			$routeClassMethodExecution = Router::extractMethodFromRoute(['routeExpression' => $findedRouteConfig['controller']]);
			#Dump::make($findedRouteConfig);
			#Dump::make($routeClassMethodExecution);
			
			# If we don't have params
			if(empty($controllerParams))
			{
				# Call controller method
				call_user_func($routeClassMethodExecution);
			}

			# If we have params
			if(!empty($controllerParams))
			{
				# Cut as excess the string: '(...)'
				$routeClassMethodExecution = explode("(", $routeClassMethodExecution)[0];

				# Call controller method
				call_user_func_array($routeClassMethodExecution, [$controllerParams]);	
			}

			# 4.1.7 Layout 'after' part for PAGE
			if($routeType == 'Page'){
				if(isset($findedRouteConfig['layout'])){
					$layoutName = ucfirst($findedRouteConfig['layout']);
					$layoutDirAfter = BACKEND_DIR . 'Layouts/' . $layoutName . '/After.php';
					require($layoutDirAfter);
				}
			}

			# 4.1.8 JS for PAGE
			if($routeType == 'Page'){
				$controllerClass = explode("::", $findedRouteConfig['controller'])[0];
				$controllerFunction = ucfirst(explode("::", $findedRouteConfig['controller'])[1]);
				$hash = Asseter::getJsHash();
				$filePath = "js/Pages"  . $controllerClass . $controllerFunction . Asseter::$jsPrefix . "." . $hash . ".js";
				$fileDir = PUBLIC_DIR . $filePath;
				if(file_exists($fileDir)){
					echo "<script src='" . PUBLIC_URL . $filePath . "'></script>";
				} else {
					//Dump::make(PUBLIC_URL . $filePath);
				}
			}
		} 

		# 	4.2. If no route is finded
		else {
			Redirect::page('');
		}

		if(Dev::is() && $routeType == 'Page'){
			echo "<div id='DevLoadCounter' class='container-fluid text-muted'>";
			echo number_format(microtime(true) - $start_time, 2) . "s";
			echo "</div>";
		}
	}
		
	#
	# 	$params = ['url']
	#
	
	public static function explodeUrl($params)
	{
		extract($params);
		# Delete all spaces
		$url = rtrim($url, '/');
		# Make as an array
		$urlParts = explode('/', $url);

		# Make strings as integers if possible
		// foreach ($urlParts as $index => &$value) {
		// 	# If route part can be numeric and doesn't contain type hinting for string
		// 	if (is_numeric($value) && !preg_match('/\:str/', $value)){
		// 		$value = intval($value);
		// 	};
		// };

		return $urlParts;
	}

	public static function extractMethodFromRoute($params)
	{
		#Dump::made($params);
		extract($params);
		$routeClassMethodPieces = explode("::", $routeExpression);
		
		# Get class name and make changes
		$routeClass = $routeClassMethodPieces[0];
		#$routeClass = strtolower($routeClass); #why?
		$routeClass = $routeClass . "_Controller";
		#$routeClass = ucfirst($routeClass);
		
		# Get method name and delete parentheses
		$routeMethod = $routeClassMethodPieces[1];
		$routeMethod = str_replace("()", "", $routeMethod);
		$routeMethod = strtolower($routeMethod);
		
		# Form possible execution expression
		return $routeClass . '::' . $routeMethod;
	}
}