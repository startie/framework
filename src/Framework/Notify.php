<?php

class Notify 
{
	#
	#	$types = success, info, warning/alert, danger
	#
	public static function add($text, $type)
	{
		#todo: why don't work class wrapper – Session
		$_SESSION['Notify'][] = ['text' => $text, 'type' => $type];
	}
	
	public static function check()
	{
		if(Session::is('Notify')){
			foreach (Session::get('Notify') as $number => &$notification) {

				if( !empty($notification['type']) ){
					$notification['type'] == "info";
					Template::render('Notify/Index', $notification);
				} else {
					Template::render('Notify/Index', $notification);
				}
			}
		}
		Session::delete('Notify');
	}

	#
	# 	Params:
	# 	$params = ['text', 'type']
	# 	
	# 	Example:
	#	::display(['text' => 'Hello', 'type' => 'success']);
	#
	
	public static function display($params)
	{
		if(empty($params['type'])){
			$params['type'] = "info";
		}

		if(!empty($params['text'])){
			Template::render('Notify/Index', $params);
		}
	}
}