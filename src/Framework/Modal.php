<?php

declare(strict_types=1);

namespace Startie;

class Modal
{
	/**
	 * Generate button class
	 * 
	 * @param $entity Example: `Post`, `User`
	 */
	public static function ButtonClass(string $entity, string $action): string
	{
		$entity = ucfirst($entity);
		$action = ucfirst($action);
		$result = "{$entity}{$action}ModalButton";

		return $result;
	}

	public static function WindowId(
		string $entity,
		string $action,
		string|int $id = ""
	): string {
		$entity = ucfirst($entity);
		$action = ucfirst($action);

		return "{$entity}{$id}{$action}Modal";
	}

	/**
	 * @params $config Syntax: `<ModelClass>::<action>`, example: `Accounts::add`
	 */
	public static function init(string $config): array
	{
		preg_match('/(\w*)::(\w*)/', $config, $matches);

		$modelClass = $matches[1];
		$modelClassWithNamespace = "\Models\\" . $modelClass;

		$action = $matches[2];
		$action = strtolower($action);

		$entity = $modelClassWithNamespace::$_;

		$modal = [];
		$modalAction = [];
		$modalAction['WindowId'] = Modal::WindowId($entity, $action);
		$modalAction['ButtonClass'] = Modal::ButtonClass($entity, $action);
		$modalAction['ButtonText'] = "+ "
			. ucfirst($action)
			. " "
			. strtolower($entity);

		$modal[$action] = $modalAction;

		return $modal;
	}

	/**
	 * Fills any entity with modal window data strings
	 * 
	 * @param $entity Can be any entity as array, for example: `$account`
	 * @param $params = [
	 * 		'Entity' => $Entity, 
	 * 		'action' => $action, 
	 * 		'idIndex' => $idIndex,
	 * ]
	 * 'Entity' example: 'Publication'
	 * 'action' example: 'edit'
	 * 'idIndex' example: 'PublicationId'
	 * 
	 * @return array Entity with additional keys:
	 * - <Action>ModalButtonClass example: EditModalButtonClass
	 * - <Action>ModalId, example: EditModalId
	 * Each key will contain a string
	 */
	public static function complete(
		array $entity,
		array $params
	): array {
		$params['action'] = ucfirst($params['action']);

		$entity[$params['action'] . 'ModalButtonClass'] = self::ButtonClass(
			$params['Entity'],
			$params['action']
		);

		$entity[$params['action'] . 'ModalId'] = self::WindowId(
			$params['Entity'],
			$params['action'],
			$entity[$params['idIndex']]
		);

		return $entity;
	}
}