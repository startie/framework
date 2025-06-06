<?php

namespace Startie;

use PDO;
use PDOException;

class Model
{
	use \Startie\Bootable;

	public static array $config;

	public static mixed $storage;

	public static array $columnTypes;

	public static array $types;

	public static string $testModeMessage = "Test mode: no write to db";

	public static array $excludeFunctions;

	public static function boot(): void
	{
		self::$isBooted = true;
		self::loadConfig();
	}

	public static function loadConfig(): void
	{
		Model::$config = Config::get('Model');
	}

	public static function storage(): \PDO
	{
		$name = Model::$config['storage']['name']; // e.g 'logs', 'common', etc.
		$connection = Db::$connections[$name];

		return $connection;
	}

    /*
    |--------------------------------------------------------------------------
	|
    |                                   CORE                                   
	|                                   
    |--------------------------------------------------------------------------
    */

	/**
	 * Creates a row in the table and returns it's id
	 * Returns 0 if in test mode
	 */
	public static function create(array $params): int
	{
		#
		#	Vars

		$die = false;
		$test = 0;

		extract($params);

		$debug ??= false;
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

		return 0;
	}

	/**
	 * Returns an array of rows (arrays) for corresponding entities
	 * Returns an empty array if there are no entries with specified params
	 * 
	 * @return array<array>
	 */
	public static function read(array $params = []): array
	{
		#
		#	Vars

		$select = ['*'];
		$table = str_replace("Models\\", "", get_called_class());
		$die = false;
		$test = 0;
		$excludeFunctions = 0;

		extract($params);

		$debug ??= false;

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
		if (isset(Db::$excludeFunctions)) {
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
	public static function update(array $params): int
	{
		$calledModelClass = get_called_class();

		// Vars
		$debug = (bool) ($params['debug'] ?? false);
		$die = (bool) ($params['die'] ?? false);
		$test = (bool) ($params['test'] ?? false);

		$set = $params['insert'] ?? $params['set'] ?? $params['fields'] ?? [];
		$where = $params['where'] ?? [];
		$having = $params['having'] ?? [];

		// Checks
		if ($where === []) {
			throw new Exception("Dangerous: no where for update");
		}

		// SQL generate
		$sql = "";
		$table = str_replace("Models\\", "", $calledModelClass);

		// if (!is_string($table)) {
		// 	throw new \Exception("Table name should be string");
		// }

		$sql .= StatementBuilder::update($table);

		StatementBuilder::set($sql, $set);
		StatementBuilder::where($sql, $where);

		#
		#	Debug

		Db::debug($debug, "Debugging `{$calledModelClass}` model");
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

		$columnTypes = $calledModelClass::$columnTypes ?? [];

		QueryBinder::set($sth, $sql, $set, $columnTypes);
		QueryBinder::clause($sth, $sql, $where);

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

		return 0;
	}

	/**
	 * Deletes rows and returns count of affected rows
	 * Return 0 if in test mode
	 */
	public static function delete(array $params): int
	{
		#
		#	Vars

		$die = false;
		$test = false;

		extract($params);

		$debug ??= false;

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

		return 0;
	}

    /*
    |--------------------------------------------------------------------------
	|
    |                                 SHORTCUTS                                
	|
    |--------------------------------------------------------------------------
    */

	/**
	 * Get count of records filtered by params
	 */
	public static function count(
		array $params,
		string|null $customSelect = null
	): int {
		$select = $params['select'] ?? '';
		if (!is_null($customSelect)) {
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
	 * Tells if exists entity with params or not
	 */
	public static function is(
		array $params,
		string|null $customSelect = NULL
	): bool {
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
	public static function id(
		int $id,
		int $debug = 0,
		int $die = 0
	): array {
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
	 * 
	 * @psalm-suppress all
	 * @deprecated 0.30.10
	 */
	public static function where(
		array $where,
		array $options = ['debug' => false, 'die' => 0, 'test' => 0]
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
	 * @psalm-suppress all
	 * @deprecated
	 */
	public static function field(
		array $where,
		array $options = ['limit' => 1, 'debug' => false, 'die' => 0, 'test' => 0]
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
	 * @psalm-suppress all
	 * @deprecated In 1.0.0 will be deleted
	 */
	public static function cnt(string $column): int
	{
		return Model::read([
			'select' => [$column],
			'order' => [$column . ' DESC'],
			'limit' => 1,
			#'debug' => true
		])[0][$column];
	}
	
    /*
    |--------------------------------------------------------------------------
	|
    |                                 UTILITY                                  
	|
    |--------------------------------------------------------------------------
    */

	/**
	 * Returns an array of columns
	 * [
	 * 	['columnName', 'columnValue', 'valueType'],
	 * 	['columnName', 'columnValue', 'valueType'],
	 * 	['columnName', 'columnValue', 'valueType'],
	 * ]
	 * 		
	 * Подойдёт для вставки в методы create и update
	 */
	public static function rows(
		array|null $exceptions = null
	): array {
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

		return $rows;
	}

	/**
	 * @param array $where Query builder entity that will be potentially filled
	 * @param array $filters Simple key-value associate array
	 * @version 0.30.0
	 */
	public static function wherify(
		array $where,
		array $filters,
		array $configs
	): array {
		if ($filters !== []) {
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
	 * @psalm-suppress all
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
		array $where,
		string $global,
		string $type,
		string $keyInGlobal,
		string|int $keyInWhere
	): array {
		if (Input::is($global, $keyInGlobal)) {
			$global = strtolower($global);
			$where[$keyInWhere] = [
				[
					call_user_func(
						'\Startie\Input::' . $global,
						$keyInGlobal,
						$type
					),
					$type
				]
			];
		}

		return $where;
	}

	public static function detach(
		array $selectFields,
		string $table,
		array $e,
		string|int $eIndex
	): array {
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

	/**
	 * Placeholder
	 */
	public static function complete(array $entity, array $params): array
	{
		return $entity;
	}

	public static function c(array $entities, array $params = []): array
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

	public static function processPdoException(
		\PDOException $e,
		string $sql,
		string $sqlBeforeBinding
	): string {
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