<?php

namespace Startie;

class QueryBinder
{
    public static function set(&$sth, &$sql, $set)
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

    public static function insert(&$sth, &$sql, $insert)
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
     * For 'where' and 'having' clauses
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