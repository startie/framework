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

		$sql = "";
		$sql .= " INSERT INTO ";
		$sql .= str_replace("Models\\", "", get_called_class());
		Model::__insert($sql, $insert);

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
			throw Exception::PDO($e, $sql);
		}

		#
		#	Bind

		Model::bindInsert($sth, $sql, $insert);

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
			throw new Exception($e);
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
		$from = str_replace("Models\\", "", get_called_class());
		$debug = 0;
		$die = 0;
		$test = 0;
		$excludeFunctions = 0;

		extract($params);

		#
		#	SQL generate

		$sql = "\n";
		Model::SELECT_($sql, $select);
		Model::FROM_($sql, $from);
		Model::JOIN_($sql, $join ?? []);
		Model::WHERE_($sql, $where ?? []);
		Model::GROUP_($sql, $group ?? []);
		Model::HAVING_($sql, $having ?? []);
		Model::ORDER_($sql, $order ?? NULL);
		Model::LIMIT_($sql, $limit ?? NULL);
		Model::OFFSET_($sql, $offset ?? NULL);
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
			throw Exception::PDO($e, $sql);
		}

		#
		#	Bind

		Model::bindClause($sth, $sql, $where ?? NULL);
		Model::bindClause($sth, $sql, $having ?? NULL);

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
			throw Exception::PDO($e, $sql);
		}
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
		$sql .= " UPDATE " . str_replace("Models\\", "", get_called_class());

		$set = $insert ?? $fields ?? $set;

		Model::SET_($sql, $set);
		Model::WHERE_($sql, $where);

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
			throw Exception::PDO($e, $sql);
		}

		#
		#	Bind

		Model::bindSet($sth, $sql, $set);
		Model::bindClause($sth, $sql, $where ?? NULL);
		Model::bindClause($sth, $sql, $having ?? NULL);

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
			throw Exception::PDO($e, $sql);
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

		#
		#	SQL generate

		$sql = "";
		$sql .= "\nDELETE\nFROM " . str_replace("Models\\", "", get_called_class()) . " \n";
		Model::WHERE_($sql, $where);

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

		Model::bindClause($sth, $sql, $where);

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
			throw Exception::PDO($e, $sql);
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

		# Возвращаем массив колонок:
		# [
		#	['columnName', 'columnValue', 'valueType'],
		# 	['columnName', 'columnValue', 'valueType'],
		# 	['columnName', 'columnValue', 'valueType'],
		# ]
		# 
		# Подойдёт для вставки в методы create и update

		return $rows;
	}

	public static function wherify($final, $source, $configs)
	{
		if (isset($source)) {
			foreach ($configs as $c => $config) {
				$field = $config[0];
				$index = $config[1];
				$type = $config[2];
				$value = $source[$index];

				if (isset($value) && $value != "") {
					$final[$field] = [[$value, $type]];
				}
			}
		}

		return $final;
	}

	/**
	 * Check if there is a value in some global array with specified key
	 * If it is presented, add this value to $where
	 * 
	 * @deprecated 0.19.20 use whereFromInput instead
	 */
	public static function isWhereInput($where, $global, $type, $keyInGlobal, $keyInWhere)
	{
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

	public static function whereFromInput(
		$where,
		$global,
		$type,
		$keyInGlobal,
		$keyInWhere
	) {
		return self::whereFromInput(
			$where,
			$global,
			$type,
			$keyInGlobal,
			$keyInWhere
		);
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

	#
	#
	#		SQL CODE GENERATORS
	#
	#

	public static function SELECT_(&$sql, array $select)
	{
		$sql .= " ";
		$sql .= "SELECT";
		$sql .= "\n\t";

		foreach ($select as $key => $value) {
			$sql .= " " . $value . ",";
			$sql .= "\n\t";
		}
		$sql = substr($sql, 0, -3);
		$sql .= "\n\n ";
	}

	public static function FROM_(&$sql, string $from)
	{
		$sql .= " ";
		$sql .= "FROM ";
		$sql .= $from;
		$sql .= "\n\n ";
	}

	public static function JOIN_(&$sql, array $join)
	{
		$sql .= " ";
		if (isset($join)) {
			foreach ($join as $tableName => $joinParams) {
				if (isset($joinParams[2])) {
					$joinType = strtoupper($joinParams[2]);
				} else {
					$joinType = "INNER";
				}
				$sql .= "$joinType JOIN ";
				$sql .= $tableName;
				$sql .= " ON";
				$sql .= " $joinParams[0] = $joinParams[1]";
				$sql .= "\n";
			}
			$sql .= "\n\n ";
		}
	}

	/**
	 * This method is required by ::WHERE_() and ::HAVING_()
	 */
	public static function CLAUSE_(&$sql, array $params, string $type): void
	{
		$sql .= " ";
		$sql .= "{$type} \t 1 = 1 ";
		$sql .= "\n";

		if (isset($params)) {
			foreach ($params as $columnName => $columnValuesArr) {
				$sql .= "\t AND ( ";
				foreach ($columnValuesArr as $i => $columnValueData) {
					# $columnValueData[0] == sign + value (sv)
					# $columnValueData[1] == type (t)

					# Get sign
					$signHolder = $columnValueData[0] ?? '';
					if (strpos($signHolder, '<=') !== false) {
						$sign = '<=';
					} else if (strpos($signHolder, '>=') !== false) {
						$sign = '>=';
					} else if (strpos($signHolder, '<') !== false) {
						$sign = '<';
					} else if (strpos($signHolder, '>') !== false) {
						$sign = '>';
					} else if (strpos($signHolder, '!') !== false) {
						$sign = '<>';
					} else if (strpos($signHolder, '=') !== false) {
						$sign = '=';
					} else {
						$sign = '=';
					};

					# A. With backticks – do not make binding
					if (strpos($signHolder, '`') !== false) {
						#Dump::make($sign);
						# Delete backticks
						$v = preg_replace('/`/', '', $signHolder);
						# A. When has LIKE, REGEXP, IN, IS NULL, IS NOT NULL
						if (
							strrpos($v, 'LIKE') !== false ||
							strrpos($v, 'REGEXP') !== false ||
							strrpos($v, 'IN') !== false ||
							strrpos($v, 'IS NULL') !== false ||
							strrpos($v, 'IS NOT NULL') !== false ||
							$v == ''
						) {
							$sql .=  $columnName . " " . $v;
							$sql .= " OR ";
						}

						# B. When has not 
						else {

							# C. When with > or < 
							if (strpos($v, '<') !== false || strpos($v, '>') !== false) {
								$sql .=  "$columnName $v";
								$sql .= " OR ";
							}

							# D. When without > or < 
							else {
								$sql .=  $columnName . " = " . $v;
								$sql .= " OR ";
							}
						}
					}

					# B. Without backticks – make binding
					else {
						#Dump::make($sign . "mb");
						# Если наша колонка будет указана с именеем таблицы через точку, то точка помешает, её и удаляем
						$filteredColumnName = str_replace('.', '', $columnName);
						# Формируем фрагмент запроса
						$sql .=  "$columnName $sign :$filteredColumnName$i";
						#Dump::make("$columnName $sign :$filteredColumnName$i");
						$sql .= " OR ";
						#Dump::make($sign);
						#Dump::make("':$filteredColumnName$i' is waiting for bind...\n");
					}
				}
				# Delete trailing "OR ("
				$sql = substr($sql, 0, -4);
				$sql .= " ) ";
				$sql .= "\n";
			}
		}

		$sql .= " ";
	}

	public static function WHERE_(&$sql, array $params)
	{
		Model::CLAUSE_($sql, $params, "WHERE");
	}
	public static function HAVING_(&$sql, array $params)
	{
		Model::CLAUSE_($sql, $params, "HAVING");
	}

	public static function GROUP_(&$sql, $group)
	{
		if (isset($group) && !empty($group)) {

			$sql .= "\n";
			$sql .= "GROUP BY";

			foreach ($group as $param) {
				$sql .= " " . $param . " , ";
			}
			$sql = substr($sql, 0, -2);
			$sql .= "\n\n";
		}
	}

	public static function ORDER_(&$sql, $order)
	{
		if (isset($order)) {

			$sql .= "\n";
			$sql .= "ORDER BY";

			foreach ($order as $param) {
				$sql .= " " . $param . ",";
			}
			$sql = substr($sql, 0, -1);
			$sql .= "\n";
		}
	}

	public static function LIMIT_(&$sql, $limit)
	{
		if (isset($limit)) {
			$sql .= "\n";
			$sql .= "LIMIT  " . $limit . " ";
			$sql .= "\n\n";
		}
	}

	public static function OFFSET_(&$sql, $offset)
	{
		if (isset($offset)) {
			$sql .= "\n";
			$sql .= "OFFSET  " . $offset . " ";
			$sql .= "\n\n";
		}
	}

	public static function SET_(&$sql, $set)
	{
		$sql .= " ";
		$sql .= "SET ";
		foreach ($set as $i => $data) {
			$col = $data[0];
			$val = $data[1];
			$type = $data[2] ?? NULL;

			# With backticks
			if (Schema::hasBackticks($val)) {
				# delete backticks
				$valClean = preg_replace('/`/', '', $val);

				$sql .= "$col = $valClean";
				$sql .= ",";
				$sql .= " ";
			}

			# Without backticks
			else {
				$sql .= "{$col} = :{$col}{$i}";
				$sql .= ",";
				$sql .= " "; # required
			}
		}
		$sql = substr($sql, 0, -2); # Deleting last comma and space
	}

	public static function __insert(&$sql, $insert)
	{
		#
		# 	Fields

		$sql .= " ( ";
		foreach ($insert as $insertArr) {
			$col = $insertArr[0];
			$sql .= "`{$col}`";
			$sql .= ",";
			$sql .= " "; # required
		}
		$sql = substr($sql, 0, -2); # Deleting last comma
		$sql .= " ) ";

		#
		# 	Values

		$sql .= " VALUES ";
		$sql .= " ( ";

		foreach ($insert as $data) {
			$col = $data[0];
			$val = $data[1];
			$type = $data[2] ?? NULL;

			# With backticks
			if (Schema::hasBackticks($val)) {
				# delete backticks
				$valClean = preg_replace('/`/', '', $val);

				$sql .= " {$valClean}";
				$sql .= ",";
				$sql .= " "; # required
			}

			# Without backticks
			else {
				$sql .= " :{$col}";
				$sql .= ",";
				$sql .= " "; # required
			}
		}
		$sql = substr($sql, 0, -2); # Deleting last comma
		$sql .= " ) ";
	}

	#
	#
	#		BIND HELPERS
	#
	#

	public static function bindSet(&$sth, &$sql, $set)
	{
		if (isset($set)) {
			foreach ($set as $i => &$data) {
				$col = $data[0];
				$val = $data[1];
				$type = $data[2] ?? NULL;
				$bindExpr = ":{$col}{$i}";

				#
				# 	With backticks

				if (Schema::hasBackticks($val)) {
					# do nothing
				}

				#
				# 	Without backticks

				else {
					# bind type
					if ($type) {
						$typeConst = constant('PDO::PARAM_' . mb_strtoupper($type));
						$sth->bindValue($bindExpr, $val, $typeConst);
					} else {
						$sth->bindValue($bindExpr, $val);
					}
				}

				#
				# 	Replace placeholders for debugging

				$sql = str_replace($bindExpr, '"' . $val . '"', $sql);
			}
		}
	}

	public static function bindInsert(&$sth, &$sql, $insert)
	{
		if (isset($insert)) {
			foreach ($insert as $i => $data) {
				$col = $data[0];
				$val = $data[1];
				$type = $data[2] ?? NULL;

				$bindExpr = ":{$col}";

				#
				# 	With backticks

				if (Schema::hasBackticks($val)) {
					# do nothing
				}

				#
				# 	Without backticks

				else {
					if ($type) {
						$typeConst = constant('PDO::PARAM_' . mb_strtoupper($type));
						$sth->bindValue($bindExpr, $val, $typeConst);
					} else {
						$sth->bindValue($bindExpr, $val);
					}
				}

				#
				# 	Replace placeholders for debugging

				$sql = str_replace($bindExpr, '"' . $val . '"', $sql);
			}
		}
	}

	/**
	 * For 'WHERE' and 'HAVING' clauses
	 */
	public static function bindClause(&$sth, &$sql, $clause)
	{
		if (isset($clause)) {
			foreach ($clause as $columnName => $columnValuesArr) {
				foreach ($columnValuesArr as $i => $data) {
					$signAndValue = $data[0] ?? '';
					$type = $data[1] ?? NULL;

					# If we have type
					if (isset($type)) {
						$valFiltered = preg_replace('/[><=!]/i', '', $signAndValue);
						#Dump::make("Have type. Value will be = $valFiltered");
						#echo ':' . $columnName . $i . "\n"; 

						$filteredColumnName = str_replace('.', '', $columnName);
						$typeConst = constant('PDO::PARAM_' . mb_strtoupper($type));
						$sth->bindValue(":{$filteredColumnName}{$i}", $valFiltered, $typeConst);
						#Dump::make("':$filteredColumnName$i' binded with $valFiltered \n");

						# Replace placeholders for debugging
						$needle = ":{$filteredColumnName}{$i}";
						$replace = '"' . $valFiltered . '"';
						$pos = strpos($sql, $needle);
						if ($pos !== false) {
							$sql = substr_replace($sql, $replace, $pos, strlen($needle));
						}
					}

					# If we don't have type
					else {
						# Если есть бэктиксы
						# - to support `< UNIX_TIMESTAMP()`
						if (Schema::hasBackticks($signAndValue)) {
							break (1);
						}

						# Если нет ни LIKE ни бэктиксов одновременно
						# - to support IS NULL
						if (strrpos($signAndValue, 'LIKE') === false && strpos($signAndValue, '`') === false) {
							# Work
							$valFiltered = ltrim(preg_replace('/[><=!]/i', '', $signAndValue));
							$filteredColumnName = str_replace('.', '', $columnName);
							#Dump::make("No type. Value will be = '$valFiltered'");
							$sth->bindValue(":{$filteredColumnName}{$i}", $valFiltered);
							#Dump::make("Column will be = :$filteredColumnName$i");
							#die();		

							# Replace placeholders for debugging
							$needle = ":{$filteredColumnName}{$i}";
							$replace = '"' . $valFiltered . '"';
							$pos = strpos($sql, $needle);
							if ($pos !== false) {
								$sql = substr_replace($sql, $replace, $pos, strlen($needle));
							}
						}
					}
				}
			}
		}
	}
}