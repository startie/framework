<?php

namespace Startie;

class Notify
{
	/**
	 * @param string $type Possible values: 
	 * - 'success'
	 * - 'info'
	 * - 'warning' or 'alert'
	 * - 'danger'
	 */
	public static function add(string $text, string $type)
	{
		$messageData = ['text' => $text, 'type' => $type];
		Session::push('Notify', $messageData);
	}

	/**
	 * Will render a notification
	 */
	public static function check()
	{
		if (Session::has('Notify')) {
			foreach (Session::get('Notify') as $number => &$notification) {

				if (!empty($notification['type'])) {
					$notification['type'] == "info";
					echo template('Notify/Index', $notification);
				} else {
					echo template('Notify/Index', $notification);
				}
			}
			unset($notification);
		}

		Session::delete('Notify');
	}

	/**
	 * @param array $params ['text', 'type']
	 * 
	 * Example: 
	 * `::display(['text' => 'Hello', 'type' => 'success'])`
	 */
	public static function display($params)
	{
		if (empty($params['type'])) {
			$params['type'] = "info";
		}

		if (!empty($params['text'])) {
			echo template('Notify/Index', $params);
		}
	}
}