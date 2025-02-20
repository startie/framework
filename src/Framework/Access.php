<?php

namespace Startie;

class Access
{
	const string PROVIDERS_NAMESPACE = '\\Providers\\';
	public static array $providers = [];

	/**
	 * Load providers from .env and init corresponding classes
	 * 
	 * @throws \Startie\Exception
	 */
	public static function init(): void
	{
		/**
		 * Load providers from .env
		 */
		if (isset($_ENV['ACCESS_PROVIDER'])) {
			self::$providers = explode(',', $_ENV['ACCESS_PROVIDER']);
		}

		if (isset($_ENV['ACCESS_PROVIDERS'])) {
			self::$providers = explode(',', $_ENV['ACCESS_PROVIDERS']);
		}

		/**
		 * Init providers
		 * For example, it will be called something like this: VkAccess::init()
		 */
		if (count(self::$providers) > 0) {
			foreach (self::$providers as $provider) {
				// Evaluate a class with a namespace like 'Providers\VkAccess'
				$AccessProviderClass = self::PROVIDERS_NAMESPACE
					. $provider
					. 'Access';

				if (class_exists($AccessProviderClass)) {
					call_user_func($AccessProviderClass . "::init");
				} else {
					$message = "Class `$AccessProviderClass` is missing. "
						. "Create it in \Providers namespace";

					throw new \Startie\Exception($message);
				}
			}
		}
	}

	/**
	 * @throws \Startie\Exception
	 */
	public static function is(string $group, int $UserId = null): bool
	{
		// Unify group string
		$group = strtolower($group);

		// 	Check if there are any loaded providers
		if (self::$providers !== []) {
			foreach (self::$providers as $provider) {
				// Evaluate a class with a namespace like 'Providers\VkAccess'
				$AccessProviderClass = self::PROVIDERS_NAMESPACE
					. $provider
					. 'Access';

				// Call it with argument $group
				$hasAccess = call_user_func(
					$AccessProviderClass . '::is',
					$group
				);

				// If 'true' return true
				if ($hasAccess) {
					return true;
				}

				// If didn't return 'true'
				// then continue the loop to check next access provider
			}

			// 	If after all iterations we didn't return true, return false
			return false;
		}

		// 	If no providers found, then throw an error
		else {
			throw new \Startie\Exception("There are no access providers defined");
		}
	}
}