<?php

// class Controller 
// {	
	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		INI, ADD
	#											
	# # # # # # # # # # # # # # # # # # # # # # #

	// public static function init()
	// {
	// 	$url = Input::get('url', 'STR');
	// 	$url = rtrim($url, '/');
	// 	$url = explode('/', $url);

	// 	if(!empty($url[0])) {
	// 		$route_name = $url[0];
	// 	} else {
	// 		$route_name = "index";
	// 	}

	// 	$route_filename = $route_name . '_controller';

	// 	if(
	// 		$route_name != "backend" 
	// 		&& 
	// 		$route_name != "favicon.ico" 
	// 		&& 
	// 		$route_name != "libraries"
	// 		&& 
	// 		$route_name != "public" 
	// 	) {

	// 		$route_path = BACKEND_CONTROLLERS_DIR . $route_name.'_controller.php';

	// 		if(isset($url[1]))
	// 			$route_method = $url[1];
	// 		if(isset($url[2]))
	// 			$route_parameter = $url[2];


	// 		require $route_path;
	// 		$route = new $route_filename;


	// 		if(isset($route_method)){
	// 			if(isset($route_parameter)) {
	// 				$route->$route_method($route_parameter);
	// 			}
	// 			else {
	// 				if(isset($url[1])) {
	// 					$route->{$url[1]}();
	// 				}
	// 			}
	// 		} else {
	// 			$route->index();
	// 		}
	// 	}
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

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		DELETE, REMOVE, CLEAR
	#											
	# # # # # # # # # # # # # # # # # # # # # # #

	# # # # # # # # # # # # # # # # # # # # # # #
	#											
	#		RENDER, SHOW
	#											
	# # # # # # # # # # # # # # # # # # # # # # #

//}