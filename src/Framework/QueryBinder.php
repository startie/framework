<?php

namespace Startie;

use Startie\Sql;

class QueryBinder
{
    public static function set(&$sth, &$sql, $set)
    {
        $log = [];

        if (isset($set)) {
            foreach ($set as $i => &$data) {
                $column = $data[0];
                $value = $data[1] ?? "";
                $type = $data[2] ?? NULL;

                $bindExpr = ":{$column}{$i}";

                // Bind type
                if (!Sql::startsWithBacktick($value)) {
                    if (!is_null($type)) {
                        self::validateType($type);

                        $typeConst = constant(
                            'PDO::PARAM_' . mb_strtoupper($type)
                        );
                        $sth->bindValue($bindExpr, $value, $typeConst);
                    } else {
                        // PDO::PARAM_STR will be used
                        $sth->bindValue($bindExpr, $value);
                    }
                }

                $sql = self::replacePlaceholdersForDump1(
                    $sql,
                    $bindExpr,
                    $value
                );
            }
        }

        return $log;
    }

    public static function insert(&$sth, &$sql, $insert)
    {
        $log = [];

        if (isset($insert)) {
            foreach ($insert as $i => $data) {
                $column = $data[0];
                $value = $data[1] ?? NULL;
                $type = $data[2] ?? "NULL";

                $bindExpression = ":{$column}";

                // Bind type
                if (!Sql::startsWithBacktick($value)) {
                    if (!is_null($type)) {
                        $type = self::validateType($type);

                        $typeConst = constant(
                            'PDO::PARAM_' . mb_strtoupper($type)
                        );
                        $sth->bindValue($bindExpression, $value, $typeConst);
                    } else {
                        // PDO::PARAM_STR will be used
                        $sth->bindValue($bindExpression, $value);
                    }
                }

                $sql = self::replacePlaceholdersForDump1(
                    $sql,
                    $bindExpression,
                    $value
                );
            }
        }

        return $log;
    }

    /**
     * For 'where' and 'having' clauses
     */
    public static function clause(&$sth, &$sql, $clause)
    {
        $log = [];

        if (!isset($clause)) {
            return null;
        }

        foreach ($clause as $column => $columnValuesArr) {
            foreach ($columnValuesArr as $i => $data) {
                $signAndValue = $data[0] ?? '';
                $type = $data[1] ?? NULL;

                // If type exists
                if (!is_null($type)) {
                    $type = self::validateType($type);

                    $valueFiltered = preg_replace(
                        '/[><=!]/i',
                        '',
                        $signAndValue
                    );

                    $bindExpr = ":{$column}{$i}";

                    $log[] = "Have type. "
                        . " Value for '$bindExpr' will be '$valueFiltered'";

                    // Remove dot from table name
                    $columnFiltered = str_replace('.', '', $column);

                    $bindExpr = ":{$columnFiltered}{$i}";

                    $typeConst = constant(
                        'PDO::PARAM_' . mb_strtoupper($type)
                    );

                    $sth->bindValue($bindExpr, $valueFiltered, $typeConst);

                    $log[] = "':$columnFiltered$i' was binded "
                        . "with '$valueFiltered'";

                    $sql = QueryBinder::replacePlaceholdersForDump2(
                        $sql,
                        $bindExpr,
                        $valueFiltered,
                    );
                }

                // If type doesn't exist
                else {
                    // If contains backticks:
                    // - to support `< UNIX_TIMESTAMP()`
                    if (Sql::startsWithBacktick($signAndValue)) {
                        break 1;
                    }

                    // Если нет nor LIKE nor backtics одновременно
                    // - to support IS NULL
                    if (
                        strrpos($signAndValue, 'LIKE') === false
                        &&
                        !Sql::startsWithBacktick($signAndValue)
                    ) {
                        $valueFiltered = ltrim(
                            preg_replace('/[><=!]/i', '', $signAndValue)
                        );

                        $columnFiltered = str_replace('.', '', $column);

                        $bindExpr = ":{$columnFiltered}{$i}";

                        $log[] = "No type. Value will be = '$valueFiltered'";

                        // PDO::PARAM_STR will be used
                        $sth->bindValue($bindExpr, $valueFiltered);

                        $log[] = "Column will be = :$columnFiltered$i";

                        $sql = QueryBinder::replacePlaceholdersForDump2(
                            $sql,
                            $bindExpr,
                            $valueFiltered,
                        );
                    }
                }
            }
        }

        return $log;
    }

    /**
     * Performs fixes
     */
    public static function fixType(string $type): string
    {
        $type = strtoupper($type);

        $type = str_replace("STRING", "STR", $type);
        $type = str_replace("INTEGER", "INT", $type);

        return $type;
    }

    /**
     * Strictly validate for PDO::PARAM_INT and PDO::PARAM_STR
     */
    public static function isValidType(string $type): bool
    {
        if (in_array($type, ['INT', 'STR'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateType(string $type)
    {
        $type = self::fixType($type);

        if (!self::isValidType($type)) {
            throw new \Startie\Exception("$type is not valid for query binder");
        } else {
            return $type;
        }
    }

    public static function replacePlaceholdersForDump1(
        $sql,
        $bindExpr,
        $value,
    ): string {
        $sql = str_replace($bindExpr, '"' . $value . '"', $sql);

        return $sql;
    }

    public static function replacePlaceholdersForDump2(
        $sql,
        $bindExpr,
        $value,
    ): string {
        $replace = '"' . $value . '"';
        $pos = strpos($sql, $bindExpr);
        if ($pos !== false) {
            $sql = substr_replace(
                $sql,
                $replace,
                $pos,
                strlen($bindExpr)
            );
        }

        return $sql;
    }
}