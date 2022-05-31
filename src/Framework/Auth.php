<?php

namespace Startie;

use Users; // Model from app's source
use Startie\Session;

class Auth
{

	#
	# 		$params = ['uid', 'ServiceId'];
	#

	public static function make($params)
	{
		$UserId = Users::getIdByProfileUid($params);

		$authEntity = [];
		$authEntity['service'] = 'app';
		$authEntity['id'] = $UserId;
		//Session::set('auth', [$authEntity]); #todo: почему не работает
		$_SESSION['auth'][] = $authEntity;
	}

	public static function ask()
	{
		if (!Auth::is()) {
			Session::set('urlBeforeLogin', Url::current());
			Redirect::page($_ENV['AUTH_ASK_FIRST']);
			die();
		} else if (!Access::is('users')) {
			Redirect::page($_ENV['AUTH_ASK_SECOND']);
		}
	}

	public static function is()
	{
		# When setup about no connection
		// if($_ENV['NO_CONNECTION']){
		// 	return true;
		// }

		# When we are connected
		// else {
		if (Session::is('auth')) {
			return true;
		}
		return false;
		// }
	}

	public static function isWithService($serviceName)
	{
		if (Session::is('auth')) {
			foreach (Session::get('auth') as $authEntity) {
				if (isset($authEntity['service'])) {
					if ($authEntity['service'] == $serviceName) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public static function getIdInService($serviceName)
	{
		if (Session::is('auth')) {
			if (Session::is('auth')) {
				foreach (Session::get('auth') as $authEntity) {
					if ($authEntity['service'] == $serviceName) {
						return $authEntity['id'];
					}
				}
			}
		}
		return false;
	}
}
