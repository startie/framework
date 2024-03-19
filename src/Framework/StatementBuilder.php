<?php

declare(strict_types=1);

namespace Startie;

/**
 * Class for generating SQL statemens
 */
class StatementBuilder
{
    /**
     * @throws Exception
     * @tested
     */
    public static function select(string &$sql, array $columns): void
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

    /**
     * @tested
     */
    public static function from(string &$sql, string $from): void
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

    /**
     * @tested
     */
    public static function join(string &$sql, array $join): void
    {
        StatementParamValidator::join($join);

        $sql .= " ";

        if (isset($join)) {
            foreach ($join as $tableName => $joinParams) {
                $joinType = $joinParams[2] ?? NULL;
                $joinType = self::resolveJoinType($joinType);

                $leftColumn = $joinParams[0];
                $rightColumn = $joinParams[1];

                $sql .= "$joinType JOIN $tableName ON $leftColumn = $rightColumn";
                $sql .= "\n";
            }

            $sql .= "\n";
            $sql .= "\n";
            $sql .= " ";
        }
    }

    /**
     * @tested
     */
    public static function resolveJoinType($joinType)
    {
        if (!is_null($joinType)) {
            $joinType = strtoupper($joinType);
        } else {
            $joinType = "INNER";
        }

        return $joinType;
    }

    /**
     * This method is required by ::where() and ::having()
     * @tested
     */
    public static function clause(
        string &$sql,
        array $params,
        string $type
    ): void {
        $sql .= " ";
        $sql .= "{$type} \t 1 = 1 ";
        $sql .= "\n";

        if (isset($params)) {
            foreach ($params as $columnName => $columnValuesArr) {
                $sql .= "\t AND ( ";
                foreach ($columnValuesArr as $i => $columnValueData) {
                    $signAndValue = $columnValueData[0];
                    // $type = $columnValueData[1] ?? NULL;

                    $signAndValue = self::validateSignAndValue(
                        $signAndValue,
                        $columnValuesArr
                    );

                    // Detect sign
                    $signAndValue = strval($signAndValue);
                    $sign = self::parseSign($signAndValue);

                    // Do not make binding
                    if (Sql::hasBacktick($signAndValue)) {
                        $sql .= self::generateRawClauses(
                            $columnName,
                            $signAndValue,
                        );
                    }

                    // Make binding
                    if (!Sql::hasBacktick($signAndValue)) {
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

    // TODO: test
    public static function parseSign(int|string $signHolder): string
    {
        $sign = '';

        $signHolder = strval($signHolder);

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

    /**
     * @tested
     */
    public static function generateRawClauses(
        string $column,
        string $signHolder
    ): string {
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
            if (
                strpos($signHolder, '<') !== false
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

    // TODO: test
    public static function generateBindedClauses(
        string $column,
        string $sign,
        $index
    ): string {
        $sql = "";

        // Если колонка будет указана с именеем таблицы через `.`
        // то точка помешает, её и удаляем
        $columnFiltered = str_replace('.', '', $column);

        // Формируем фрагмент запроса
        $placeholder = self::generatePlaceholder($columnFiltered, $index);
        $sql .=  "$column $sign $placeholder";
        $sql .= " OR ";

        return $sql;
    }

    // TODO: test
    public static function validateSignAndValue(
        int|string $data,
        array $columnValuesArr
    ): int|string {
        if (is_null($data)) {
            throw new Exception(
                'Sign and value can not be null in params'
                    . json_encode($columnValuesArr)
            );
        } else {
            return $data;
        }
    }

    // TODO: test
    public static function where(string &$sql, array $params): void
    {
        StatementParamValidator::where($params);

        self::clause($sql, $params, "WHERE");
    }

    // TODO: test
    public static function having(string &$sql, array $params): void
    {
        self::clause($sql, $params, "HAVING");
    }

    // TODO: test
    public static function group(string &$sql, array $group): void
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

    // TODO: test
    public static function order(string &$sql, array|null $order): void
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

    // TODO: test
    public static function limit(string &$sql, $limit): void
    {
        if (isset($limit)) {
            $sql .= "\n";
            $sql .= "LIMIT  $limit ";
            $sql .= "\n";
            $sql .= "\n";
        }
    }

    // TODO: test
    public static function offset(string &$sql, $offset): void
    {
        if (isset($offset)) {
            $sql .= "\n";
            $sql .= "OFFSET  $offset ";
            $sql .= "\n";
            $sql .= "\n";
        }
    }

    // TODO: test
    public static function set(string &$sql, array $set): void
    {
        $sql .= " ";
        $sql .= "SET";
        $sql .= " ";

        foreach ($set as $index => $data) {
            $col = $data[0];
            $val = $data[1] ?? "";

            // With backticks
            if (Sql::startsWithBacktick($val)) {
                // delete backticks
                $valClean = preg_replace('/`/', '', $val);

                $sql .= "$col = $valClean";
                $sql .= ",";
                $sql .= " ";
            }

            // Without backticks
            else {
                $placeholder = self::generatePlaceholder($col, $index);
                $sql .= "$col = $placeholder, "; // trailing space is required
            }
        }

        // Delete last comma and space
        $sql = substr($sql, 0, -2);
    }

    /**
     * @tested
     */
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
                $placeholder = self::generatePlaceholder($column);
                $sql .= " $placeholder, "; // trailing space is required
            }
        }

        // Deleting last comma
        $sql = substr($sql, 0, -2);
        $sql .= " ) ";
    }

    /**
     * @tested
     */
    public static function update(string $table): string
    {
        $sql = " UPDATE $table ";
        return $sql;
    }

    /**
     * @tested
     */
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
     * TODO: test
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

    /**
     * Generate identifier for named parameter
     * 
     * TODO: test
     */
    public static function generatePlaceholder(
        string $column,
        int|string|null $index = ""
    ): string {
        $placeholder = ":{$column}{$index}";
        return $placeholder;
    }
}