<?php

class Modal
{
	public static function ButtonClass($Entity, $action)
	{
		$Entity = ucfirst($Entity);
		$action = ucfirst($action);
		$result = "{$Entity}{$action}ModalButton";
		//Dump::made($result);
		return $result;
	}

	public static function WindowId($Entity, $action, $id = "")
	{
		$Entity = ucfirst($Entity);
		$action = ucfirst($action);

		return "{$Entity}{$id}{$action}Modal";
	}

	public static function init($config)
	{
		preg_match('/(\w*)::(\w*)/', $config, $matches);
		$class = $matches[1];
		$action = $matches[2];

		$Entity = ($class::$_);

		$Modal = [];
		$ModalAction = [];
		$ModalAction['WindowId'] = Modal::WindowId($Entity, $action);
		$ModalAction['ButtonClass'] = Modal::ButtonClass($Entity, $action);
		$ModalAction['ButtonText'] = "+ " . Php::mb_ucfirst($action) . " " . mb_strtolower($Entity);

		$Modal[$action] = $ModalAction;

		return $Modal;
	}

	public static function complete(
		$item,
		$params
		//  = [
		// 	'Entity' => $Entity, 
		// 	'action' => $action, 
		// 	'idIndex' => $idIndex,
		// ]
	) {
		$params['action'] = ucfirst($params['action']);
		$item[$params['action'] . 'ModalButtonClass'] = self::ButtonClass($params['Entity'], $params['action']);
		$item[$params['action'] . 'ModalId'] = self::WindowId($params['Entity'], $params['action'], $item[$params['idIndex']]);
		return $item;
	}
}
