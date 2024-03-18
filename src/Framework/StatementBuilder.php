<?php

namespace Startie;

/**
 * Class for generating SQL statemens
 */
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
                    // $columnValueData[0] == sign + value (sv)
                    // $columnValueData[1] == type (t)

                    if(is_null($columnValueData[0])){
                        throw new Exception(
                            'Sign and value can not be null in params'
                            . json_decode($columnValuesArr)
                        );
                    };

                    // Detect sign
                    $signAndValue = $columnValueData[0];
                    $sign = self::detectSign($signAndValue);

                    // a) With backticks: do not make binding
                    if (strpos($signAndValue, '`') !== false) {
                        $sql .= self::generateRawClauses(
                            $columnName,
                            $signAndValue, 
                        );
                    }

                    // b) Without backticks: make binding
                    if (strpos($signAndValue, '`') === false) {
                        $sql .= self::generateBindedClauses(
                            $columnName, 
                            $sign, 
                            $i,
                        );
                    }
                }

                // Delete trailing "OR ("
                $sql = substr($sql, 0, -4);
                $sql .= " ) ";
                $sql .= "\n";
            }
        }

        $sql .= " ";
    }

    public static function detectSign($signHolder)
    {
        $sign = '';

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
        }

        return $sign;
    }

    public static function generateRawClauses(
        string $column, 
        string $signHolder
    ): string
    {
        $sql = "";
        
        // Delete backticks
        $signHolder = preg_replace('/`/', '', $signHolder);

        // a) when has LIKE, REGEXP, IN, IS NULL, IS NOT NULL
        if (
            strrpos($signHolder, 'LIKE') !== false 
            ||
            strrpos($signHolder, 'REGEXP') !== false 
            ||
            strrpos($signHolder, 'IN') !== false 
            ||
            strrpos($signHolder, 'IS NULL') !== false
            ||
            strrpos($signHolder, 'IS NOT NULL') !== false 
            ||
            $signHolder == ''
        ) {
            $sql .= "{$column} {$signHolder}";
        }

        // b) when has not those
        else {

            // a) when with > or < 
            if (strpos($signHolder, '<') !== false 
                || 
                strpos($signHolder, '>') !== false
            ) {
                $sql .= "{$column} {$signHolder}";
            }

            // b) When without those
            else {
                $sql .=  "{$column} = {$signHolder}";
            }
        }

        $sql .= " OR ";

        return $sql;
    }

    public static function generateBindedClauses($column, $sign, $index)
    {
        $sql = "";

        // Если колонка будет указана с именеем таблицы через `.`
        // то точка помешает, её и удаляем
        $columnFiltered = str_replace('.', '', $column);

        // Формируем фрагмент запроса
        $sql .=  "{$column} {$sign} :{$columnFiltered}{$index}";
        $sql .= " OR ";

        return $sql;
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
            $val = $data[1] ?? "";

            # With backticks
            if (Sql::startsWithBacktick($val)) {
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

    public static function insert(string &$sql, array $insert, string $table)
    {
        $sql .= " INSERT INTO {$table} ";

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
            $value = $insertItem[1] ?? "";

            // With backticks
            if (Sql::startsWithBacktick($value)) {
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

    /**
     * Generates: `LIKE '%$value%'`
     */
    public static function like($value): string
    {
        $result = "";

        $result .= "LIKE ";
        $result .= "'";
        $result .= "%";
        $result .= $value;
        $result .= "%";
        $result .= "'";

        return $result;
    }
}