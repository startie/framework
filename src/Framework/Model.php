<?php

namespace Startie;

use PDO;
use PDOException;

class Model
{
	use \Startie\Bootable;

	public static $config;

	public static $storage;

	public static $types;

	public static $testModeMessage = "Test mode: no write to db";

	public static $excludeFunctions;

	public static function boot()
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static function loadConfig()
	{
		Model::$config = Config::get('Model');
	}

	public static function storage()
	{
		$name = Model::$config['storage']['name']; // e.g 'logs', 'common', etc.
		$connection = Db::$connections[$name];

		return $connection;
	}

	#
	#
	#		CORE
	#
	#

	/**
	 * Creates a row in the table and returns it's id
	 */
	public static function create(array $params): int|string
	{
		#
		#	Vars

		$debug = 0;
		$die = 0;
		$test = 0;

		extract($params);

		$insert = $fields ?? $insert;
		$insert = $set ?? $insert;

		#
		#	SQL generate

		$table = str_replace("Models\\", "", get_called_class());

		$sql = "";
		StatementBuilder::insert($sql, $insert, $table);

		#
		#	Debug

		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $params);

		#
		#	Prepare

		$db = Model::storage();
		try {
			$sth = $db->prepare($sql);
		} catch (PDOException $e) {
			throw Exception::create($e, $sql);
		}

		#
		#	Bind

		$sqlBeforeBinding = $sql;

		QueryBinder::insert($sth, $sql, $insert);

		#
		#	Debug

		Db::debugContinue($debug, $sth);
		Db::debugEnd($debug, $sql);
		if ($test) {
			Dump::e("Test mode (create): no write to db");
		}

		#
		#	Execute

		if ($die) die();

		try {
			if (!$test) {
				$sth->execute();
				$lastInsertedId = $db->lastInsertId();
				$lastInsertedId = intval($lastInsertedId);
				return $lastInsertedId;
			}
		} catch (PDOException $e) {
			self::processPdoException($e, $sql, $sqlBeforeBinding);
		}
	}

	/**
	 * Returns an array of rows (arrays) for corresponding entities
	 */
	public static function read(array $params = []): array
	{
		#
		#	Vars

		$select = ['*'];
		$table = str_replace("Models\\", "", get_called_class());
		$debug = 0;
		$die = 0;
		$test = 0;
		$excludeFunctions = 0;

		extract($params);

		#
		#	SQL generate

		$sql = "\n";
		StatementBuilder::select($sql, $select);
		StatementBuilder::from($sql, $table);
		StatementBuilder::join($sql, $join ?? []);
		StatementBuilder::where($sql, $where ?? []);
		StatementBuilder::group($sql, $group ?? []);
		StatementBuilder::having($sql, $having ?? []);
		StatementBuilder::order($sql, $order ?? NULL);
		StatementBuilder::limit($sql, $limit ?? NULL);
		StatementBuilder::offset($sql, $offset ?? NULL);
		$sql .= "";

		#
		#	Exclude functions

		# Env settings
		if (Db::$excludeFunctions) {
			# Param
			if ($excludeFunctions) {
				#Dump::make($sql);
				foreach (Db::$excludeFunctions as $excludeFunction) {
					$sql = str_replace($excludeFunction, "", $sql);
				}
				#Dump::made($sql);
			}
		}

		#
		#	Debug

		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $params);

		#
		#	Prepare

		$db = Model::storage();
		try {
			$sth = $db->prepare($sql);
		} catch (PDOException $e) {
			throw Exception::create($e, $sql);
		}

		#
		#	Bind

		$sqlBeforeBinding = $sql;

		if (isset($where)) {
			QueryBinder::clause($sth, $sql, $where);
		}

		if (isset($having)) {
			QueryBinder::clause($sth, $sql, $having);
		}

		#
		#	Debug

		Db::debugContinue($debug, $sth);
		Db::debugEnd($debug, $sql);
		if ($test) {
			Dump::e("Test mode (read): no write to db");
		}

		#
		#	Execute

		if ($die) die();
		try {
			if (!$test) {
				$sth->execute();
				$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $rows;
			}
		} catch (PDOException $e) {
			self::processPdoException($e, $sql, $sqlBeforeBinding);
		}

		return [];
	}

	/**
	 * Updates rows and returns count of affected rows
	 */
	public static function update($params): int
	{
		#
		#	Vars

		$debug = 0;
		$die = 0;
		$test = 0;

		extract($params);

		#
		#	Checks

		if (!isset($where)) {
			throw new Exception("Dangerous: no where for update");
			//die(); // after throw die is not reachable
		}

		#
		#	SQL generate

		$sql = "";

		$table = str_replace("Models\\", "", get_called_class());
		$sql .= StatementBuilder::update($table);

		$set = $insert ?? $fields ?? $set;

		StatementBuilder::set($sql, $set);
		StatementBuilder::where($sql, $where);

		#
		#	Debug

		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $params);

		#
		#	Prepare

		$db = Model::storage();
		try {
			$sth = $db->prepare($sql);
		} catch (PDOException $e) {
			throw Exception::create($e, $sql);
		}

		#
		#	Bind

		$sqlBeforeBinding = $sql;

		QueryBinder::set($sth, $sql, $set);

		if (isset($where)) {
			QueryBinder::clause($sth, $sql, $where);
		}

		if (isset($having)) {
			QueryBinder::clause($sth, $sql, $having);
		}

		#
		#	Debug

		Db::debugContinue($debug, $sth);
		Db::debugEnd($debug, $sql);
		if ($test) {
			Dump::e("Test mode (update): no write to db");
		}

		#
		#	Execute

		if ($die) die();

		try {
			if (!$test) {
				$sth->execute();
				$affectedRowsCount = $sth->rowCount();
				return $affectedRowsCount;
			}
		} catch (PDOException $e) {
			self::processPdoException($e, $sql, $sqlBeforeBinding);
		}
	}

	/**
	 * Deletes rows and returns count of affected rows
	 */
	public static function delete($params): int
	{
		#
		#	Vars

		$debug = 0;
		$die = 0;
		$test = 0;

		extract($params);

		$table = str_replace("Models\\", "", get_called_class());

		#
		#	SQL generate

		$sql = "";
		$sql .= StatementBuilder::delete();
		StatementBuilder::from($sql, $table);
		StatementBuilder::where($sql, $where);

		#
		#	Debug

		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $params);

		#
		#	Prepare

		$db = Model::storage();
		$sth = $db->prepare($sql);

		#
		#	Bind

		$sqlBeforeBinding = $sql;

		QueryBinder::clause($sth, $sql, $where);

		#
		#	Debug

		Db::debugContinue($debug, $sth);
		Db::debugEnd($debug, $sql);
		if ($test) {
			Dump::e("Test mode (delete): no write to db");
		}

		#
		#	Execute

		if ($die) die();

		try {
			if (!$test) {
				$sth->execute();
				$affectedRowsCount = $sth->rowCount();
				return $affectedRowsCount;
			}
		} catch (PDOException $e) {
			self::processPdoException($e, $sql, $sqlBeforeBinding);
		}
	}

	#
	#
	#		SHORTCUTS
	#
	#

	/**
	 * Get count of records filtered by params
	 */
	public static function count(array $params, string|null $customSelect = NULL): int
	{
		$select = $params['select'] ?? '';
		if ($customSelect) {
			$select = ["count($customSelect) as count"];
		} else {
			$select = ["count(*) as count"];
		}
		$params['select'] = $select;

		$result = self::read($params);
		if (!$result) {
			return 0;
		} else {
			return $result[0]["count"];
		}
	}

	/**
	 * 
	 * Tells if exists entity with params or not
	 */
	public static function is(array $params, string|null $customSelect = NULL): bool
	{
		$count = self::count($params, $customSelect);
		if ($count <= 0) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Returns a first row for specified id in the table
	 */
	public static function id($id, $debug = 0, $die = 0): array
	{
		$rows = self::read([
			'where' => [
				'id' => [[$id, 'INT']]
			],
			'debug' => $debug
		]);

		$row = $rows[0] ?? [];
		return $row;
	}

	/**
	 * Returns rows with specified 'where' only
	 * 
	 * ```
	 * $Entity = Entity::where([
	 * 		'column' => [['value', 'TYPE']]
	 * ]);
	 * ```
	 */
	public static function where(
		$where,
		$options = ['debug' => 0, 'die' => 0, 'test' => 0]
	): array {
		extract($options);

		$result = self::read([
			'where' => $where,
			'debug' => $debug,
			'die' => $die,
			'test' => $test,
		]);

		return $result;
	}

	/**
	 * Select only one row with certain field value
	 *
	 * ```php
	 * $Entity = Entity::field([
	 * 		'column' => [[$val, 'TYPE']]
	 * ]);
	 * ```
	 * 
	 * @deprecated
	 */
	public static function field(
		$where,
		$options = ['limit' => 1, 'debug' => 0, 'die' => 0, 'test' => 0]
	) {
		extract($options);

		$result = self::read([
			'where' => $where,
			'limit' => $limit,
			'debug' => $debug,
			'die' => $die,
			'test' => $test,
		]);

		if (count($result) == 1 && $limit == 1) {
			return $result[0];
		} else {
			return $result;
		}
	}

	/**
	 * 
	 * @deprecated
	 */
	public static function cnt($column)
	{
		return Model::read([
			'select' => [$column],
			'order' => [$column . ' DESC'],
			'limit' => 1,
			#'debug' => 1
		])[0][$column];
	}

	#
	#
	#		UTILITY
	#
	#

	public static function rows($exceptions = NULL)
	{
		$rows = [];

		# Получаем конфиг типов данных из текущей модели
		$types = static::$types;

		foreach (array_keys($_POST) as $i => $var) {
			# Если данная переменная есть в исключениях использовать её значение из исключения, иначе из POST
			$value = (!empty($exceptions[$var])) ? $exceptions[$var] : $_POST[$var];

			# Получаем тип данных у переменной
			$fieldType = NULL;
			foreach ($types as $type => $fields) {
				foreach ($fields as $i => $field) {
					if ($var == $field) $fieldType = $type;
				}
			}

			if (empty($fieldType)) {
				foreach ($types as $type => $fields) {
					foreach ($fields as $i => $field) {
						if ($field == "*") $fieldType = $type;
					}
				}
			}

			# Обрабатываем checkbox
			if ($value == "on" && $fieldType == "INT") {
				$value = 1;
			}

			# Обрабатываем пустую строку, которая должна быть числом
			if ($value == "" && $fieldType == "INT") {
				$value = NULL;
			}

			$rows[] = [$var, $value, $fieldType];

			unset($fieldType);
		}

		/*
			Return an array of columns
			[
				['columnName', 'columnValue', 'valueType'],
				['columnName', 'columnValue', 'valueType'],
				['columnName', 'columnValue', 'valueType'],
			]
			
			Подойдёт для вставки в методы create и update
		*/

		return $rows;
	}

	/**
	 * @param array $where Query builder entity that will be potentially filled
	 * @param array $filters Simple key-value associate array
	 * @version 0.30.0
	 */
	public static function wherify(array $where, array $filters, array $configs)
	{
		if (isset($filters)) {
			foreach ($configs as $config) {
				$column = $config[0];
				$filterAttribute = $config[1];
				$type = $config[2];

				if (isset($filters[$filterAttribute])) {
					$value = $filters[$filterAttribute];

					if (isset($value) && $value != "") {
						$where[$column] = [[$value, $type]];
					}
				}
			}
		}

		return $where;
	}

	/**
	 * Check if there is a value in some global array with specified key
	 * If it is presented, add this value to $where
	 * 
	 * @deprecated 0.19.20 use whereFromInput instead
	 */
	public static function isWhereInput($where, $global, $type, $keyInGlobal, $keyInWhere)
	{
		return self::whereFromInput(
			$where,
			$global,
			$type,
			$keyInGlobal,
			$keyInWhere
		);
	}

	public static function whereFromInput(
		$where,
		$global,
		$type,
		$keyInGlobal,
		$keyInWhere
	) {
		if (Input::is($global, $keyInGlobal)) {
			$global = strtolower($global);
			$where[$keyInWhere] = [
				[
					call_user_func('\Startie\Input::' . $global, $keyInGlobal, $type),
					$type
				]
			];
		}
		return $where;
	}

	public static function detach($selectFields, $table, $e, $eIndex)
	{
		$fieldsNeeded = [];

		foreach ($selectFields as $fieldExpr) {
			$isNeeded = strpos($fieldExpr, ucfirst($table) . ".");
			if ($isNeeded === 0 || $isNeeded > 0) {
				$pattern = "/[\S\s]*" . $table . "\.[\S\s]*as /";
				$fieldExpr = preg_replace($pattern, "", $fieldExpr);
				$pattern = "/[\s]/";
				$fieldExpr = preg_replace($pattern, "", $fieldExpr);
				$pattern = "/" . $table . "\./";
				$fieldExpr = preg_replace($pattern, "", $fieldExpr);

				$fieldsNeeded[] = $fieldExpr;
			}
		}

		$newEntity = [];

		foreach ($fieldsNeeded as &$fieldName) {
			$newEntity[$fieldName] = $e[$fieldName];
		}

		foreach ($fieldsNeeded as $fieldName) {
			unset($e[$fieldName]);
		}

		$e[$eIndex] = $newEntity;

		return $e;
	}

	public static function c($entities, $params = [])
	{
		foreach ($entities as &$entity) {
			$entity = static::complete($entity, $params);
		}

		return $entities;
	}

	public static function getHint(PDOException $e): string
	{
		if (str_contains(
			$e->getMessage(),
			"Invalid parameter number: parameter was not defined"
		)) {
			return 'there is possible problem with placeholders, try to debug generated SQL';
		} else {
			return "";
		}
	}

	public static function processPdoException($e, $sql, $sqlBeforeBinding)
	{
		$hint = self::getHint($e);
		throw \Startie\Exception::create(
			$e,
			" "
				. "Hint: "
				. $hint
				. ". SQL code before binding:"
				. $sqlBeforeBinding
				. ". SQL code:"
				. $sql
		);
	}
}