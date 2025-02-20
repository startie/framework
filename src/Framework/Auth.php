<?php

declare(strict_types=1);

namespace Startie;

use \Startie\Session;
use \Models\Users;
use Startie\Config;

class Auth
{
	use \Startie\Bootable;

	public static array $config;

	public static function boot(): void
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static function loadConfig(): void
	{
		self::$config = Config::get('Auth');
	}

	#
	# 		$params = ['uid', 'ServiceId'];
	#

	public static function make(array $params): void
	{
		$UserId = Users::getIdByProfileUid($params);

		$authEntity = [];
		$authEntity['service'] = 'app';
		$authEntity['id'] = $UserId;

		// TODO: почему не работает
		//Session::set('auth', [$authEntity]); 
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

		if (Session::has('auth')) {
			return true;
		}

		return false;
		// }
	}

	public static function isWithService(string $serviceName): bool
	{
		if (Session::has('auth')) {
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

	public static function getIdInService(string $serviceName): int|bool
	{
		if (Session::has('auth')) {
			if (Session::has('auth')) {
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
	 * @return never
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