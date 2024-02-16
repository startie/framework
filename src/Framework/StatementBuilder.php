<?php

namespace Startie;

class StatementBuilder
{
    /**
     * @throws Exception
     */
    public static function select(string &$sql, array $columns)
    {
        StatementParamValidator::select($columns);

        $sql .= " ";
        $sql .= "SELECT";
        $sql .= "\n";
        $sql .= "\t";

        foreach ($columns as $index => $column) {
            $sql .= " $column, ";
            $sql .= "\n";
            $sql .= "\t";
        }

        $sql = substr($sql, 0, -4);
        $sql .= "\n";
        $sql .= "\n";
        $sql .= " ";
    }

    public static function from(string &$sql, string $from)
    {
        // offset from `SELECT` OR `DELETE` part
        $sql .= " ";

        $sql .= "FROM";
        $sql .= " ";
        $sql .= $from;
        $sql .= "\n";
        $sql .= "\n";

        // offset just in case
        $sql .= " ";
    }

    public static function join(string &$sql, array $join)
    {
        StatementParamValidator::join($join);

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
            $sql .= "\n";
            $sql .= "\n";
            $sql .= " ";
        }
    }

    /**
     * This method is required by ::where() and ::having()
     */
    public static function clause(string &$sql, array $params, string $type): void
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

    public static function where(string &$sql, array $params)
    {
        StatementParamValidator::where($params);

        self::clause($sql, $params, "WHERE");
    }

    public static function having(string &$sql, array $params)
	{
		self::clause($sql, $params, "HAVING");
	}

    public static function group(string &$sql, array $group)
	{
		if (isset($group) && !empty($group)) {
			$sql .= "\n";
			$sql .= "GROUP BY";

			foreach ($group as $param) {
				$sql .= " $param , ";
			}

			$sql = substr($sql, 0, -2);
			$sql .= "\n";
			$sql .= "\n";
		}
	}

    public static function order(string &$sql, array|null $order)
	{
		if (isset($order)) {

			$sql .= "\n";
			$sql .= "ORDER BY";

			foreach ($order as $param) {
				$sql .= " $param,";
			}
			$sql = substr($sql, 0, -1);
			$sql .= "\n";
		}
	}

    public static function limit(string &$sql, $limit)
	{
		if (isset($limit)) {
			$sql .= "\n";
			$sql .= "LIMIT  $limit ";
			$sql .= "\n";
			$sql .= "\n";
		}
	}

	public static function offset(string &$sql, $offset)
	{
		if (isset($offset)) {
			$sql .= "\n";
			$sql .= "OFFSET  $offset ";
			$sql .= "\n";
            $sql .= "\n";
		}
	}

	public static function set(string &$sql, $set)
	{
		$sql .= " ";
		$sql .= "SET ";

		foreach ($set as $index => $data) {
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
				$sql .= "{$col} = :{$col}{$index}";
				$sql .= ",";
				$sql .= " "; # required
			}
		}

		$sql = substr($sql, 0, -2); # Deleting last comma and space
	}

	public static function insert(string &$sql, array $insert)
	{
        $sql .= " ";
        $sql .= "INSERT INTO";
        $sql .= " ";

		#
		# 	Fields

		$sql .= " ( ";
		foreach ($insert as $insertItem) {
			$col = $insertItem[0];
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

		foreach ($insert as $insertItem) {
			$column = $insertItem[0];
			$value = $insertItem[1];
			// $type = $insertItem[2] ?? NULL; // not used

			// With backticks
			if (Schema::hasBackticks($value)) {
				// Delete backticks
				$valueClean = preg_replace(
                    '/`/', 
                    '', 
                    $value
                );

				$sql .= " {$valueClean}";
				$sql .= ",";
				$sql .= " "; // required
			}

			// Without backticks
			else {
				$sql .= " :{$column}";
				$sql .= ",";
				$sql .= " "; // required
			}
		}

        // Deleting last comma
		$sql = substr($sql, 0, -2); 
		$sql .= " ) ";
	}

    public static function update(string $table): string
    {
        $sql = " UPDATE $table ";
        return $sql;
    }

    public static function delete(): string
    {
        $sql = "";
        $sql .= "\n";
        $sql .= "DELETE";
        $sql .= "\n";
        return $sql;
    }
}