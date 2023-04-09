<?php

namespace Startie;

use \Startie\Access;
use \Startie\Session;
use \Models\Users;

class Auth
{
	use \Startie\Bootable;

	public static function boot()
	{
		self::$isBooted = true;

		// Loads config file

		$ConfigCommonPath = App::path('backend/Config/Auth/Common.php');
		if (file_exists($ConfigCommonPath)) {
			$ConfigCommon = require $ConfigCommonPath;
		} else {
			throw new \Startie\Exception("Couldn't boot the 'Auth' class: path '$ConfigCommonPath' doesn't exist");
		}
	}

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

	/**
	 * Requires authentication
	 *
	 * @deprecated
	 * @return void
	 */
	public static function ask()
	{
		throw new \Startie\Exception("Deprecated. Create instead Middles/Auth/require");
		//die(); // after throw die is not reachable

		// self::requireBoot();

		// if (!self::is()) {
		// 	Session::set('urlBeforeLogin', Url::current());
		// 	Redirect::to(self::$redirects['ask'][0]);
		// 	die();
		// } else if (!Access::is('users')) {
		// 	Redirect::to(self::$redirects['ask'][1]);
		// }
	}
}
