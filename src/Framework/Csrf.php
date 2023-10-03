<?php

namespace Startie;

use Startie\Url;
use Startie\Input;
use Startie\Dump;
use Startie\Auth;
use DateTime;
use DateInterval;

class Csrf
{
	public static function create(): void
	{
		# Generate token's hash
		$csrfToken = bin2hex(openssl_random_pseudo_bytes(32));

		if (Input::is('POST', 'csrfUrl')) {
			$csrfUrl = Input::post('csrfUrl', 'STR');
			$url = str_replace(Url::app(), "", $csrfUrl);
		} else {
			$url = Input::get('url', 'STR');
		}

		# Forming entity
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

		# Store token
		//Dump::made($entity);
		//Dump::made($url);
		$_SESSION['csrf'][$url][] = $entity;
	}

	public static function check()
	{
		$CurrentUserId = Auth::getIdInService('app');
		$CurrentUserId = ($CurrentUserId) ? $CurrentUserId : 0;

		# Get token from POST
		$tokenPOST = Input::post('csrfToken', 'STR');

		# Get referer url
		if (Input::is('POST', 'csrfUrl')) {
			# from POST
			$csrfUrl = Input::post('csrfUrl', 'STR');
		} else {
			# or casually
			$csrfUrl = str_replace(Url::app(), "", $_SERVER['HTTP_REFERER']);
		}

		# Find url in csrf block
		foreach ($_SESSION['csrf'] as $u => $urlConfigs) {
			if ($csrfUrl == $u) {
				foreach ($urlConfigs as $c => $urlConfig) {
					# If ok
					if (Csrf::is($tokenPOST)) {
						unset($_SESSION['csrf'][$u][$c]);
					}

					# If not
					else {
						// Logs::create([
						// 	'insert' => [
						// 		['createdAt', '`UTC_TIMESTAMP()`'],
						// 		['UserId', $CurrentUserId, 'INT'],
						// 		['line', __LINE__],
						// 		['file', __FILE__],
						// 		['url', $csrfUrl],
						// 		['message', 'Попытка CSRF c IP-адреса: ' . Users::getIpAddress(),],
						// 		['type', 'error'],
						// 		['object', 'security']
						// 	]
						// ]);
					}
				}
			}
		}
	}

	public static function is($token)
	{
		if (hash_equals($_SESSION['csrf']['token'], $token)) {
			return false;
		} else {
			return true;
		}
	}

	public static function clean()
	{
		foreach ($_SESSION['csrf'] as $u => $urlConfigs) {

			# If we have url with no congigs
			if (empty($urlConfigs)) {
				# clear it
				unset($_SESSION['csrf'][$u]);
			}

			# Otherwise find some expired configs for url
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
