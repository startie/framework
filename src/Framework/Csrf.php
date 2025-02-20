<?php

declare(strict_types=1);

namespace Startie;

use Startie\Url;
use Startie\Input;
use Startie\Auth;
use DateTime;
use DateInterval;

class Csrf
{
	public static function create(): void
	{
		// Generate token's hash
		$csrfToken = bin2hex(openssl_random_pseudo_bytes(32));

		if (Input::is('POST', 'csrfUrl')) {
			$csrfUrl = Input::post('csrfUrl', 'STR');
			$url = str_replace(Url::app(), "", $csrfUrl);
		} else {
			$url = Input::get('url', 'STR');
		}

		// Form entity
		$entity = [];

		$entity['token'] = $csrfToken;

		$createdAt = new DateTime();
		$createdAt->format('Y-m-d H:i:s');
		$entity['createdAt'] = $createdAt;

		$expiresAt = new DateTime();
		$interval = new DateInterval('PT12H');
		$expiresAt->add($interval);
		$expiresAt->format('Y-m-d H:i:s');
		$entity['expiresAt'] = $expiresAt;

		$createdBy = Auth::getIdInService('app');
		$entity['createdBy'] = $createdBy;

		// Store token
		$_SESSION['csrf'][$url][] = $entity;
	}

	public static function check(): void
	{
		$CurrentUserId = Auth::getIdInService('app');
		$CurrentUserId = ($CurrentUserId) ? $CurrentUserId : 0;

		// Get token from POST
		$tokenPOST = Input::post('csrfToken', 'STR');

		// Get referer URL
		if (Input::is('POST', 'csrfUrl')) {
			// ... from POST
			$csrfUrl = Input::post('csrfUrl', 'STR');
		} else {
			// or casually
			$csrfUrl = str_replace(
				Url::app(),
				"",
				$_SERVER['HTTP_REFERER']
			);
		}

		// Find URL in 'csrf' block
		foreach ($_SESSION['csrf'] as $u => $urlConfigs) {
			if ($csrfUrl == $u) {
				foreach ($urlConfigs as $c => $urlConfig) {
					# If ok
					if (Csrf::is($tokenPOST)) {
						unset($_SESSION['csrf'][$u][$c]);
					}

					# If not
					else {
						// TODO: log
					}
				}
			}
		}
	}

	public static function is(string $token): bool
	{
		if (hash_equals($_SESSION['csrf']['token'], $token)) {
			return false;
		} else {
			return true;
		}
	}

	public static function clean(): void
	{
		if (isset($_SESSION['csrf'])) {
			foreach ($_SESSION['csrf'] as $u => $urlConfigs) {
				// If we have URL with no congigs
				if (empty($urlConfigs)) {
					// ... clear it
					unset($_SESSION['csrf'][$u]);
				}

				// Otherwise find some expired configs for URL
				else {
					foreach ($urlConfigs as $c => $urlConfig) {
						$now = new DateTime();
						$expiresAt = $urlConfig['expiresAt'];

						if ($now > $expiresAt) {
							unset($_SESSION['csrf'][$u][$c]);
						}
					}
				}
			}
		}
	}
}