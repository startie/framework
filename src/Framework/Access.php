<?php

class Access
{	
	public static $providers = [];

	public static function init()
	{
		if(isset($_ENV['ACCESS_PROVIDER'])){
			self::$providers = explode(',', $_ENV['ACCESS_PROVIDER']);
		};
	}

	#
	#	Example:
	#	Access:is('admins')	
	
	public static function is($group)
	{
		#
		# 	Unify group string
		# 	e.g, 'Admins' => 'admins'
		
		$group = strtolower($group);

		#
		# 	Check if there are providers from .env
		
		if(is_array(self::$providers)){

			#
			# 	Loop through all of them
			
			foreach (self::$providers as $provider) {

				#
				# 	Evaluate class and method (e.g VkAccess::is)
				
				$classMethod = $provider . 'Access::is';
				
				#
				# 	Call it with param '$group', if true return true
				
				if(call_user_func($classMethod, $group)){
					return true;
				}
				
				# If not – continue the loop
			}
			
			#
			# 	If after all iterations we don't get true, return false
			
			return false;
		} 

		#
		# 	If no providers found – return the error and stop an app
		
		else {
			die('No access providers');
		}
	}
}