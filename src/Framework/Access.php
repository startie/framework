<?php

namespace Startie;

use Models\UserProfiles;

class Access
{
	const PROVIDERS_NAMESPACE = '\\Providers\\';
	public static $providers = [];

	public static function init()
	{
		// Load providers

		if (isset($_ENV['ACCESS_PROVIDER'])) {
			self::$providers = explode(',', $_ENV['ACCESS_PROVIDER']);
		};

		if (count(self::$providers) === 0) {
			if (isset($_ENV['ACCESS_PROVIDERS'])) {
				self::$providers = explode(',', $_ENV['ACCESS_PROVIDERS']);
			}
		}

		// Init providers
		// calling this: VkAccess::init()

		if (count(self::$providers) > 0) {
			foreach (self::$providers as $provider) {
				$AccessProviderClass = self::PROVIDERS_NAMESPACE . $provider . 'Access'; // 'Providers\VkAccess'
				if (class_exists($AccessProviderClass)) {
					call_user_func($AccessProviderClass . "::init");
				} else {
					throw new \Startie\Exception("Class `$AccessProviderClass` is missing. Create it in \Providers namespace");
				}
			}
		}

		// Check existance of providers

		foreach (self::$providers as $provider) {
			$AccessProviderClass = self::PROVIDERS_NAMESPACE . $provider . 'Access'; // 'Providers\VkAccess'
			if (!class_exists($AccessProviderClass)) {
				throw new \Startie\Exception("Class `$AccessProviderClass` is missing. Create it in \Providers namespace");
			}
		}
	}

	public static function is($group, $UserId = null)
	{
		if ($UserId) {
			$UserProfiles = UserProfiles::where([
				'UserId' => [[$UserId, 'INT']]
			]);
		}

		//	Unify group string
		// 	e.g, 'Admins' => 'admins'

		$group = strtolower($group);

		// 	Check if there are providers from .env

		if (is_array(self::$providers)) {

			foreach (self::$providers as $provider) {

				// 	Evaluate class and method

				$AccessProviderClass = 'Providers\\' . $provider . 'Access'; // 'Providers\VkAccess'
				$СlassAndMethod = $AccessProviderClass . '::is'; // 'Providers\VkAccess::is'

				// 	Call it with param '$group'

				$hasAccess = call_user_func($СlassAndMethod, $group);

				// If 'true' – return true

				if ($hasAccess) {
					return true;
				}

				// If 'false' – continue the loop
			}

			//
			// 	If after all iterations we don't get true, return false

			return false;
		}

		// 	If no providers found, then return the error and stop the app

		else {
			throw new \Startie\Exception("There are no defined Access providers");
		}
	}
}
