<?php

namespace Startie;

use \Startie\Session;
use \Models\Users;
use Startie\Config;

class Auth
{
	use \Startie\Bootable;

	public static $config;

	public static function boot()
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static function loadConfig()
	{
		self::$config = Config::get('Auth');
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

	public static function is(): bool
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

	public static function isWithService($serviceName): bool
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

	/**
	 * @return integer|bool
	 */
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
