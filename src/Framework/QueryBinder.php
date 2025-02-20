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
        array|null $set,
        array $columnTypes = [],
    ): array {
        $log = [];

        if (isset($set)) {
            foreach ($set as $i => &$data) {
                $column = $data[0];
                /*
                    Set NULL in case of values was not passed.
                    
                    Earlier NULL was replaced with "" but in case of DATE type 
                    it leads to DBMS stopped working.
                */
                $value = $data[1] ?? NULL;
                $type = $data[2] ?? $columnTypes[$column] ?? NULL;

                /*
                    Nullable check
                */

                /**
                 * @var bool $typeIsNullable
                 */
                $typeIsNullable = str_ends_with($type ?? "", "|NULL");
                if ($typeIsNullable) {
                    $type = str_replace("|NULL", "", $type);
                }
                if (($value === NULL) && $typeIsNullable) {
                    $type = "NULL";
                }

                $placeholder = StatementBuilder::generatePlaceholder(
                    $column,
                    $i
                );

                // Bind type
                if (!Sql::startsWithBacktick($value)) {
                    if (!is_null($type)) {
                        self::validateType($type, $column);

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
        array|null $insert = null,
        array $columnTypes = [],
    ): array {
        $log = [];

        if (isset($insert)) {
            foreach ($insert as $i => $data) {
                $column = $data[0];
                /*
                    Set NULL in case of values was not passed.
                    
                    Earlier NULL was replaced with "" but in case of DATE type 
                    it leads to DBMS stopped working.
                */
                $value = $data[1] ?? NULL;
                $type = $data[2] ?? $columnTypes[$column] ?? NULL;

                /*
                    Nullable check
                */

                /**
                 * @var bool $typeIsNullable
                 */
                $typeIsNullable = str_ends_with($type ?? "", "|NULL");
                if ($typeIsNullable) {
                    $type = str_replace("|NULL", "", $type);
                }
                if (($value === NULL) && $typeIsNullable) {
                    $type = "NULL";
                }

                $placeholder = StatementBuilder::generatePlaceholder($column);

                // Bind type
                if (!Sql::startsWithBacktick($value)) {
                    if (!is_null($type)) {
                        $type = self::validateType($type, $column);

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
        array|null $clause = null
    ): array|null {
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
                    $type = self::validateType($type, $column);

                    $valueFiltered = preg_replace(
                        '/[><=!]/i',
                        '',
                        $signAndValue
                    );

                    $placeholder = StatementBuilder::generatePlaceholder(
                        $column,
                        $i
                    );

                    $log[] = "Have type. "
                        . " Value for '$placeholder' will be '$valueFiltered'";

                    // Remove dot from table name
                    $columnFiltered = str_replace('.', '', $column);

                    $placeholder =  StatementBuilder::generatePlaceholder(
                        $columnFiltered,
                        $i
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
     * Strictly validate for PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_NULL
     * 
     * @tested
     */
    public static function isValidType(string $type): bool
    {
        if (in_array($type, ['INT', 'STR', 'NULL'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @tested
     */
    public static function validateType(string $type, string $column): string
    {
        $type = self::fixType($type);

        if (!self::isValidType($type)) {
            throw new \Startie\Exception(
                "$type is not valid for query binder"
                    . ". Debug info: column is `" . $column . "`"
            );
        } else {
            return $type;
        }
    }

    // TODO: test
    public static function replacePlaceholdersForDump1(
        string $sql,
        string $placeholder,
        int|string|null|float $value,
    ): string {
        $sql = str_replace($placeholder, '"' . $value . '"', $sql);

        return $sql;
    }

    // TODO: test
    public static function replacePlaceholdersForDump2(
        string $sql,
        string $placeholder,
        int|string|null|float $value,
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