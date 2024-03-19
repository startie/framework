<?php

declare(strict_types=1);

namespace Startie;

use Startie\Sql;
use PDOStatement;

class QueryBinder
{
    // TODO: test
    public static function set(
        PDOStatement &$sth,
        string &$sql,
        array $set
    ): array {
        $log = [];

        if (isset($set)) {
            foreach ($set as $i => &$data) {
                $column = $data[0];
                $value = $data[1] ?? "";
                $type = $data[2] ?? NULL;

                $placeholder = StatementBuilder::generatePlaceholder($column, $i);

                // Bind type
                if (!Sql::startsWithBacktick($value)) {
                    if (!is_null($type)) {
                        self::validateType($type);

                        $typeConst = constant(
                            'PDO::PARAM_' . mb_strtoupper($type)
                        );
                        $sth->bindValue($placeholder, $value, $typeConst);
                    } else {
                        // PDO::PARAM_STR will be used
                        $sth->bindValue($placeholder, $value);
                    }
                }

                $sql = self::replacePlaceholdersForDump1(
                    $sql,
                    $placeholder,
                    $value
                );
            }
        }

        return $log;
    }

    // TODO: test
    public static function insert(
        PDOStatement &$sth,
        string &$sql,
        array $insert
    ): array {
        $log = [];

        if (isset($insert)) {
            foreach ($insert as $i => $data) {
                $column = $data[0];
                $value = $data[1] ?? NULL;
                $type = $data[2] ?? "NULL";

                $placeholder = StatementBuilder::generatePlaceholder($column);

                // Bind type
                if (!Sql::startsWithBacktick($value)) {
                    if (!is_null($type)) {
                        $type = self::validateType($type);

                        $typeConst = constant(
                            'PDO::PARAM_' . mb_strtoupper($type)
                        );
                        $sth->bindValue($placeholder, $value, $typeConst);
                    } else {
                        // PDO::PARAM_STR will be used
                        $sth->bindValue($placeholder, $value);
                    }
                }

                $sql = self::replacePlaceholdersForDump1(
                    $sql,
                    $placeholder,
                    $value
                );
            }
        }

        return $log;
    }

    /**
     * For 'where' and 'having' clauses
     * 
     * TODO: test
     */
    public static function clause(
        PDOStatement &$sth,
        string &$sql,
        array $clause
    ): array {
        $log = [];

        if (!isset($clause)) {
            return null;
        }

        foreach ($clause as $column => $columnValuesArr) {
            foreach ($columnValuesArr as $i => $data) {
                $signAndValue = $data[0] ?? '';
                $signAndValue = strval($signAndValue);
                
                $type = $data[1] ?? NULL;

                // If type exists
                if (!is_null($type)) {
                    $type = self::validateType($type);

                    $valueFiltered = preg_replace(
                        '/[><=!]/i',
                        '',
                        $signAndValue
                    );

                    $placeholder = StatementBuilder::generatePlaceholder(
                        $column, $i
                    );

                    $log[] = "Have type. "
                        . " Value for '$placeholder' will be '$valueFiltered'";

                    // Remove dot from table name
                    $columnFiltered = str_replace('.', '', $column);

                    $placeholder =  StatementBuilder::generatePlaceholder(
                        $columnFiltered, $i
                    );

                    $typeConst = constant(
                        'PDO::PARAM_' . mb_strtoupper($type)
                    );

                    $sth->bindValue($placeholder, $valueFiltered, $typeConst);

                    $log[] = "':$columnFiltered$i' was binded "
                        . "with '$valueFiltered'";

                    $sql = QueryBinder::replacePlaceholdersForDump2(
                        $sql,
                        $placeholder,
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

                        $placeholder = StatementBuilder::generatePlaceholder(
                            $columnFiltered,
                            $i
                        );

                        $log[] = "No type. Value will be = '$valueFiltered'";

                        // PDO::PARAM_STR will be used
                        $sth->bindValue($placeholder, $valueFiltered);

                        $log[] = "Column will be = :$columnFiltered$i";

                        $sql = QueryBinder::replacePlaceholdersForDump2(
                            $sql,
                            $placeholder,
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
     * 
     * @tested
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
     * 
     * @tested
     */
    public static function isValidType(string $type): bool
    {
        if (in_array($type, ['INT', 'STR'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @tested
     */
    public static function validateType(string $type)
    {
        $type = self::fixType($type);

        if (!self::isValidType($type)) {
            throw new \Startie\Exception("$type is not valid for query binder");
        } else {
            return $type;
        }
    }

    // TODO: test
    public static function replacePlaceholdersForDump1(
        string $sql,
        string $placeholder,
        int|string $value,
    ): string {
        $sql = str_replace($placeholder, '"' . $value . '"', $sql);

        return $sql;
    }

    // TODO: test
    public static function replacePlaceholdersForDump2(
        string $sql,
        string $placeholder,
        int|string $value,
    ): string {
        $replace = '"' . $value . '"';
        $pos = strpos($sql, $placeholder);
        if ($pos !== false) {
            $sql = substr_replace(
                $sql,
                $replace,
                $pos,
                strlen($placeholder)
            );
        }

        return $sql;
    }
}