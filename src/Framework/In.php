<?php

class In
{
	public static function post($name, $type, $if = [], $processing = [], $replacements = [])
	{
		$data = Input::post($name, $type);
		
		#
		# 	$if[0] – if condition for processing
		# 	$if[1] – true case
		# 	$if[2] – false case 
		#
		
		if(!empty($if))
		{
			if ($data === $if[0]) {
				$data = $if[1];
			} else {
				if(!empty($if[2])){
					$data = $if[2];
				}	else {
					$data = $data;
				}
			}
		} 

		#
		# 	$processing – array of functions
		# 	
		
		if(!empty($processing) && $data)
		{
			foreach ($processing as $f) {
				$data = call_user_func($f, $data);
			}
		}

		#
		# 	$replacements – array of replacements
		# 
		
		if(!empty($replacements))
		{
			foreach ($replacements as $r) {
				preg_replace($r[0], $r[1], $data);
			}
		}

		return $data;
	}

	public static function get($name, $type, $if = [], $processing = [], $replacements = [])
	{
		$data = Input::get($name, $type);
		
		#
		# 	$if[0] – if condition for processing
		# 	$if[1] – true case
		# 	$if[2] – false case 
		#
		
		if(!empty($if))
		{
			if ($data == $if[0]) {
				$data = $if[1];
			}
		} 

		#
		# 	$processing – array of functions
		# 	
		
		if(!empty($processing) && $data)
		{
			foreach ($processing as $f) {
				$data = call_user_func($f, $data);
			}
		}

		#
		# 	$replacements – array of replacements
		# 
		
		if(!empty($replacements))
		{
			foreach ($replacements as $r) {
				preg_replace($r[0], $r[1], $data);
			}
		}

		return $data;
	}
}