<?php

class Model
{
	#
	#
	#		VARS
	#
	#
	
	public static $testModeMessage = "Test mode: no write to db";
	public static $excludeFunctions;

	public static function init()
	{
		$envindex = 'DB_EXCLUDE_FUNCTIONS';

		if($_ENV[$envindex]){
			self::$excludeFunctions = explode(",", $_ENV[$envindex]);
		}
	}

	#
	#
	#		CORE
	#
	#
	
	#
	#	Description:
	# 	returns INT – id of created row
	
	public static function create($p)
	{		
		#
		#	Vars
		
		$debug = 0;
		$die = 0;
		$test = 0;

		extract($p);

		// $insert = $fields ? $fields : $insert;
		// $insert = $set ? $set : $insert;

		#
		#	Form
		
		$sql = "";
		$sql .= " INSERT INTO ";
		$sql .= get_called_class();
		Model::__insert($sql, $insert);

		#
		#	Debug
		
		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $p);

		#
		#	State
		 
		try
		{	
			#
			#	Prepare

			$sth = DB::$h->prepare($sql);

			#
			#	Bind

			Model::bindInsert($sth, $sql, $insert);
			
			#
			#	Debug

			Db::debugContinue($debug, $sth);
			Db::debugEnd($debug, $sql);
			if($test){
				Dump::e("Test mode (create): no write to db");
			}

			#
			#	Execute
			
			if($die) die();
			if(!$test){
				$sth->execute();
				$lastInsertedId = DB::$h->lastInsertId();
				return $lastInsertedId;
			}
		} 
		
		catch (PDOException $e) {	
			AppLogs::generateFromPdoException($e);
		} 
	}

	#
	#	Description:
	# 	returns ARRAY – array of arrays (entities)
	
	public static function read($p = [])
	{
		#
		#	Vars
		
		$select = ['*'];
		$from = get_called_class();
		$debug = 0;
		$die = 0;
		$test = 0;
		$excludeFunctions = 0;

		extract($p);

		#
		#	Form
		
		$sql = "\n";
		Model::__select($sql, $select);
		Model::__from($sql, $from);
		Model::__join($sql, $join);
		Model::__where($sql, $where);
		Model::__group($sql, $group);
		Model::__having($sql, $having);
		Model::__order($sql, $order);
		Model::__limit($sql, $limit);
		Model::__offset($sql, $offset);
		$sql .= "";
		
		#
		#	Exclude functions

		# Env settings
		if(self::$excludeFunctions){
			# Param
			if($excludeFunctions){
				#Dump::make($sql);
				foreach (self::$excludeFunctions as $excludeFunction) {
					$sql = str_replace($excludeFunction, "", $sql);
				}
				#Dump::made($sql);
			}
		}

		#
		#	Debug
		
		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $p);

		try	
		{	
			#
			#	Prepare
			
			$sth = DB::$h->prepare($sql);

			#
			#	Bind

			Model::bindClause($sth, $sql, $where);
			Model::bindClause($sth, $sql, $having);

			#
			#	Debug

			Db::debugContinue($debug, $sth);
			Db::debugEnd($debug, $sql);
			if($test) {
				Dump::e("Test mode (read): no write to db");
			}

			#
			#	Execute
				
			if($die) die();
			if(!$test){
				$sth->execute();
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $result;	
			} 
			
		} 
		
		catch (PDOException $e)
		{	
			AppLogs::generateFromPdoException($e);
		}
	}

	#
	#	Description:
	#	returns INT – count of affected rows
	
	public static function update($p)
	{
		#
		#	Vars

		$debug = 0;
		$die = 0;
		$test = 0;

		extract($p);

		#
		#	Checks
		
		if(!isset($where)){
			Dump::made("Dangerous: no where for update");
			die();
		}

		#
		#	Form
				
		$sql = "";
		$sql .= " UPDATE " . get_called_class();
		Model::_set($sql, $set);
		Model::__where($sql, $where);

		#
		#	Debug
		
		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $p);

		#
		#	State

		try 
		{	
			#
			#	Prepare
				
			$sth = DB::$h->prepare($sql);

			#
			#	Bind
			
			Model::bindSet($sth, $sql, $set);
			Model::bindClause($sth, $sql, $where);
			Model::bindClause($sth, $sql, $having);

			#
			#	Debug

			Db::debugContinue($debug, $sth);
			Db::debugEnd($debug, $sql);
			if($test){
				Dump::e("Test mode (update): no write to db");
			}

			#
			#	Execute

			if($die) die();
			if(!$test){
				$sth->execute();
				$affectedRowsCount = $sth->rowCount();
				return $affectedRowsCount;
			} 
		} 
		
		catch (PDOException $e) 
		{	
			AppLogs::generateFromPdoException($e);
		}
	}

	#
	#	Description:
	#	returns INT – count of affected rows
	
	public static function delete($p)
	{
		#
		#	Vars
		
		$debug = 0;
		$die = 0;
		$test = 0;

		extract($p);

		#
		#	Form
		
		$sql = "";
		$sql .= "\nDELETE\nFROM " . get_called_class() . " \n";
		Model::__where($sql, $where);

		#
		#	Debug
		
		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $p);

		#
		#	State

		try 
		{	
			#
			#	Prepare
				
			$sth = DB::$h->prepare($sql);

			#
			#	Bind

			Model::bindClause($sth, $sql, $where);

			#
			#	Debug

			Db::debugContinue($debug, $sth);
			Db::debugEnd($debug, $sql);
			if($test) {
				Dump::e("Test mode (delete): no write to db");
			}

			#
			#	Execute
			
			if($die) die();
			if(!$test){
				$sth->execute();
				$affectedRowsCount = $sth->rowCount();
				return $affectedRowsCount;
			}
		} 
		
		catch (PDOException $e) 
		{	
			AppLogs::generateFromPdoException($e);
		}
	}

	#
	#
	#		ADDITIONAL
	#
	#

	#
	#	Description:
	# 	returns INT – sum count
	
	public static function count($p, $customSelect = null)
	{
		#
		#	Vars
		
		$select = ['*'];
		$from = get_called_class();
		$debug = 0;
		$die = 0;
		$test = 0;

		extract($p);

		#
		#	Form

		$sql = "\n\n";
		$sql .= " SELECT ";
		if($customSelect){
			#Dump::made($customSelect);
			$sql .= "count($customSelect) as count";
		} else {
			$sql .= "count(*) as count";
		}
		$sql .= "\n\n";
		Model::__from($sql, $from);
		Model::__join($sql, $join);
		Model::__where($sql, $where);
		Model::__group($sql, $group);
		Model::__having($sql, $having);
		Model::__order($sql, $order);
		Model::__limit($sql, $limit);
		Model::__offset($sql, $offset);
		$sql .= "\n";
		
		#
		#	Debug
		
		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $p);
		
		#
		#	State
		
		try	
		{	
			#
			#	Prepare
				
			$sth = DB::$h->prepare($sql);

			#
			#	Bind
			
			Model::bindClause($sth, $sql, $where);
			
			#
			#	Debug

			Db::debugContinue($debug, $sth);
			Db::debugEnd($debug, $sql);
			if($test) {
				Dump::e("Test mode (count): no write to db");
			}

			#
			#	Execute
			
			if($die) die();
			if(!$test)
			{
				$sth->execute();
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				if(!$result){
					return 0;
				} else {
					return $result[0]["count"];
				}
			}			
		} 
		
		catch (PDOException $e) 
		{	
			AppLogs::generateFromPdoException($e);
		}
	}
	
	#
	#	Description:
	# 	returns BOOLEAN – exists entity with param or not

	public static function is($p)
	{
		#
		#	Vars
		
		$select = ['*'];
		$from = get_called_class();
		$debug = 0;
		$die = 0;
		$test = 0;

		extract($p);

		#
		#	Form
		
		$sql = "\n\n";
		$sql .= " SELECT ";
		$sql .= "count(*)";
		$sql .= "\n\n";
		Model::__from($sql, $from);
		Model::__join($sql, $join);
		Model::__where($sql, $where);
		Model::__group($sql, $group);
		Model::__having($sql, $having);
		Model::__order($sql, $order);
		Model::__limit($sql, $limit);
		Model::__offset($sql, $offset);
		$sql .= "";
		
		#
		#	Debug
		
		Db::debug($debug, "Debugging '" . get_called_class() . "' model");
		Db::debugStart($debug, $p);
		
		#
		#	State
		
		try	
		{	
			#
			#	Prepare
				
			$sth = DB::$h->prepare($sql);

			#
			#	Bind
			
			Model::bindClause($sth, $sql, $where);

			#
			#	Debug
			# 

			Db::debugContinue($debug, $sth);
			Db::debugEnd($debug, $sql);
			if($test){
				Dump::e("Test mode (is): no write to db");
			}
		
			#
			#	Execute
				
			if($die) die();
			if(!$test)
			{
 				$sth->execute();
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				if($result[0]["count(*)"]){
					return true;	
				} else {
					return false;	
				}
			}
			
		} 
		
		catch (PDOException $e) 
		{	
			AppLogs::generateFromPdoException($e);
		}
	}

	#
	#
	#		SHORTCUTS
	#
	#
	
	public static function id($id, $debug = 0)
	{
		return self::read([
			'where' => [
				'id' => [[$id, 'INT']]
			],
			'debug' => $debug
		])[0];
	}

	#
	#	$Entity = Entity::field(['field' => [[$val, 'TYPE']]]);
	#	
	#	$where = [
	#		$field => [[$value, $type]]
	#	], 

	public static function field(
		$where, 
		$options = ['limit' => 1, 'debug' => 0, 'die' => 0, 'test' => 0])
	{
		extract($options);

		$result = self::read([
			'where' => $where,
			'limit' => $limit,
			'debug' => $debug,
			'die' => $die,
			'test' => $test,
		]);

		if(count($result) == 1 && $limit == 1){
			return $result[0];
		} else {
			return $result;
		}
	}

	public static function cnt($column)
	{
		return self::read([
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

		foreach (array_keys($_POST) as $i => $var) 
		{
			# Если данная переменная есть в исключениях использовать её значение из исключения, иначе из POST
			$value = (!empty($exceptions[$var])) ? $exceptions[$var] : $_POST[$var];
			
			# Получаем тип данных у переменной
			$fieldType = NULL;
			foreach ($types as $type => $fields) {
				foreach ($fields as $i => $field) {
					if($var == $field) $fieldType = $type;
				}
			}

			if(empty($fieldType)){
				foreach ($types as $type => $fields) {
					foreach ($fields as $i => $field) {
						if($field == "*") $fieldType = $type;
					}
				}
			}

			# Обрабатываем checkbox
			if($value == "on" && $fieldType == "INT"){
				$value = 1;
			}

			# Обрабатываем пустую строку, которая должна быть числом
			if($value == "" && $fieldType == "INT"){
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
		if(isset($source))
		{
			foreach ($configs as $c => $config) 
			{				
				$field = $config[0];
				$index = $config[1];
				$type = $config[2];
				$value = $source[$index];

				if(isset($value) && $value != ""){
					$final[$field] = [[$value, $type]];
				}
			}
		}

		return $final;
	}

	#
	#	Description:
	#	check if there is value in $global with $g(index)
	#	then add this value to $where with $w(index)
	
	public static function isWhereInput($where, $global, $type, $gindex, $windex)
	{
		if(Input::is($global, $gindex))
		{
			$global = strtolower($global);
			$where[$windex] = [
				[call_user_func('Input::' . $global, $gindex, $type), $type]
			];
		}
		return $where;
	}

	public static function detach($selectFields, $table, $e, $eIndex)
	{
		$fieldsNeeded = [];

		foreach ($selectFields as $fieldExpr) 
		{
			$isNeeded = strpos($fieldExpr, ucfirst($table) . ".");
			if($isNeeded === 0 || $isNeeded > 0)
			{
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

		foreach ($fieldsNeeded as &$fieldName) 
		{
			$newEntity[$fieldName] = $e[$fieldName];
		}

		foreach ($fieldsNeeded as $fieldName) {
			unset($e[$fieldName]);
		}

		$e[$eIndex] = $newEntity;

		return $e;
	}

	public static function c($Entities, $params = [])
	{
		foreach ($Entities as &$e) {
			$e = static::complete($e, $params);
		}
		return $Entities;
	}

	#
	#
	#		FORM HELPERS
	#
	#
	
	public static function __select(&$sql, $select)
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

	public static function __from(&$sql, $from)
	{
		$sql .= " ";
		$sql .= "FROM ";
		$sql .= $from;
		$sql .= "\n\n ";
	}

	public static function __join(&$sql, $join)
	{
		$sql .= " ";
		if(isset($join)){
			foreach ($join as $tableName => $joinParams)
			{
				if($joinParams[2]){
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

	#	на этом методе работают __where() и __having()
	public static function __clause(&$sql, $params, $type)
	{
		$sql .= " ";
		$sql .= "{$type} \t 1 = 1 ";
		$sql .= "\n";

		if(isset($params))
		{
			foreach ($params as $columnName => $columnValuesArr) 
			{
				$sql .= "\t AND ( ";
				foreach ($columnValuesArr as $i => $columnValueData) 
				{
					# $columnValueData[0] == sign + value (sv)
					# $columnValueData[1] == type (t)
							 
					# Get sign
					$signHolder = $columnValueData[0];
					if 		(strpos($signHolder, '<=') !== false) 	{ $sign = '<='; }
					else if (strpos($signHolder, '>=') !== false) 	{ $sign = '>='; }
					else if (strpos($signHolder, '<') !== false) 	{ $sign = '<';  }
					else if (strpos($signHolder, '>') !== false) 	{ $sign = '>';  } 
					else if (strpos($signHolder, '!') !== false) 	{ $sign = '<>';  } 
					else if (strpos($signHolder, '=') !== false) 	{ $sign = '=';  }
					else 											{ $sign = '=';  };

					# A. With backticks – do not make binding
					if (strpos($columnValueData[0], '`') !== false)
					{
						#Dump::make($sign);
						# Delete backticks
						$v = preg_replace('/`/', '', $columnValueData[0]);
						# A. When has LIKE, REGEXP, IN, IS NULL, IS NOT NULL
						if(
							strrpos($v, 'LIKE') !== false || 
							strrpos($v, 'REGEXP') !== false || 
							strrpos($v, 'IN') !== false ||
							strrpos($v, 'IS NULL') !== false || 
							strrpos($v, 'IS NOT NULL') !== false ||
							$v == ''
						){
							$sql .=  $columnName . " " . $v;
							$sql .= " OR ";
						} 

						# B. When has not 
						else {

							# C. When with > or < 
							if(strpos($v, '<') !== false || strpos($v, '>') !== false){
								$sql .=  "$columnName $v";
								$sql .= " OR ";
							} 

							# D. When without > or < 
							else {
								$sql .=  $columnName . " = ". $v;
								$sql .= " OR ";
							}
							
						}
					}

					# B. Without backticks – make binding
					else 
					{
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

	public static function __where(&$sql, $params){ return self::__clause($sql, $params, "WHERE"); }
	public static function __having(&$sql, $params){ return self::__clause($sql, $params, "HAVING"); }

	public static function __group(&$sql, $group)
	{
		if(isset($group) && !empty($group)){
			
			$sql .= "\n";
			$sql .= "GROUP BY";

			foreach ($group as $param) {
				$sql .= " " . $param . " , ";
			}
			$sql = substr($sql, 0, -2);
			$sql .= "\n\n";
		}
	}

	public static function __order(&$sql, $order)
	{
		if(isset($order)){
			
			$sql .= "\n";
			$sql .= "ORDER BY";

			foreach ($order as $param) {
				$sql .= " " . $param . ",";
			}
			$sql = substr($sql, 0, -1);
			$sql .= "\n";
		}
	}

	public static function __limit(&$sql, $limit)
	{
		if(isset($limit)){
			$sql .= "\n";
			$sql .= "LIMIT  " . $limit . " ";
			$sql .= "\n\n";
		}
	}

	public static function __offset(&$sql, $offset)
	{
		if(isset($offset)){
			$sql .= "\n";
			$sql .= "OFFSET  " . $offset . " ";
			$sql .= "\n\n";
		}
	}

	public static function _set(&$sql, $set)
	{
		$sql .= " ";
		$sql .= "SET ";
		foreach ($set as $i => $data) 
		{
			$col = $data[0];
			$val = $data[1];
			$type = $data[2];
					
			# With backticks
			if (Schema::hasBt($val)) 
			{
				# delete backticks
				$valClean = preg_replace('/`/', '', $val);

				$sql .= "$col = $valClean";
				$sql .= ",";
				$sql .= " ";
			}

			# Without backticks
			else 
			{
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
		foreach ($insert as $insertArr) 
		{	
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

		foreach ($insert as $data)
		{
			$col = $data[0];
			$val = $data[1];
			$type = $data[2];
			
			# With backticks
			if (Schema::hasBt($val)) 
			{
				# delete backticks
				$valClean = preg_replace('/`/', '', $val);

				$sql .= " {$valClean}";
				$sql .= ",";
				$sql .= " "; # required
			} 

			# Without backticks
			else 
			{
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
		if(isset($set))
		{
			foreach ($set as $i => &$data)
			{
				$col = $data[0]; 
				$val = $data[1]; 
				$type = $data[2];
				$bindExpr = ":{$col}{$i}";

				#
				# 	With backticks
				
				if (Schema::hasBt($val)) 
				{
					# do nothing
				}

				#
				# 	Without backticks
				
				else 
				{
					# bind type
					if($type){
						$typeConst = constant('PDO::PARAM_' . mb_strtoupper($type));
						$sth->bindValue($bindExpr, $val, $typeConst);
					} else {
						$sth->bindValue($bindExpr, $val);
					}
				}

				#
				# 	Replace placeholders for debugging
				
				$sql = str_replace($bindExpr, '"'.$val.'"', $sql);
			}
		}	
	}

	public static function bindInsert(&$sth, &$sql, $insert)
	{
		if(isset($insert))
		{
			foreach ($insert as $i => $data) 
			{				
				$col = $data[0];
				$val = $data[1];
				$type = $data[2];
				$bindExpr = ":{$col}";

				#
				# 	With backticks
				
				if (Schema::hasBt($val)) 
				{
					# do nothing
				}

				#
				# 	Without backticks
				
				else {
					if($type){
						$typeConst = constant('PDO::PARAM_' . mb_strtoupper($type));
						$sth->bindValue($bindExpr, $val, $typeConst);
					} else {
						$sth->bindValue($bindExpr, $val);
					}
				}

				#
				# 	Replace placeholders for debugging
				
				$sql = str_replace($bindExpr, '"'.$val.'"', $sql);
			}
		}
	}

	# for 'where' and 'having'
	public static function bindClause(&$sth, &$sql, $where)
	{
		if(isset($where))
		{
			foreach ($where as $columnName => $columnValuesArr)
			{
				foreach ($columnValuesArr as $i => $data) 
				{
					$signAndValue = $data[0];
					$type = $data[1];

					# If we have type
					if(isset($type))
					{
						$valFiltered = preg_replace('/[><=!]/i', '', $signAndValue);
						#Dump::make("Have type. Value will be = $valFiltered");
						#echo ':' . $columnName . $i . "\n"; 
						
						$filteredColumnName = str_replace('.', '', $columnName);
						$typeConst = constant('PDO::PARAM_' . mb_strtoupper($type));
						$sth->bindValue(":{$filteredColumnName}{$i}", $valFiltered, $typeConst);
						#Dump::make("':$filteredColumnName$i' binded with $valFiltered \n");
						
						# Replace placeholders for debugging
						$needle = ":{$filteredColumnName}{$i}";
						$replace = '"'.$valFiltered.'"';
						$pos = strpos($sql, $needle);
						if ($pos !== false) {
						    $sql = substr_replace($sql, $replace, $pos, strlen($needle));
						}

					} 

					# If we don't have type
					else 
					{
						# Если есть бэктиксы
						# - to support `< UNIX_TIMESTAMP()`
						if(Schema::hasBt($signAndValue)){
							break(1);
						}

						# Если нет ни LIKE ни бэктиксов одновременно
						# - to support IS NULL
						if(strrpos($signAndValue, 'LIKE') === false && strpos($signAndValue, '`') === false)
						{
							# Work
							$valFiltered = ltrim(preg_replace('/[><=!]/i', '', $signAndValue));
							$filteredColumnName = str_replace('.', '', $columnName);
							#Dump::make("No type. Value will be = '$valFiltered'");
							$sth->bindValue(":{$filteredColumnName}{$i}", $valFiltered);
							#Dump::make("Column will be = :$filteredColumnName$i");
							#die();		
							
							# Replace placeholders for debugging
							$needle = ":{$filteredColumnName}{$i}";
							$replace = '"'.$valFiltered.'"';
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