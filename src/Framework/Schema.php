<?php

namespace Startie;

class Schema
{
    public static function hasBackticks($val)
    {
        if (strpos($val ?? "", '`') === 0) {
            return true;
        } else {
            return false;
        }
    }

    // #todo: transfer to the new class Mysql
    // #todo: rename to jsonRegExp
    // since 5.6
    // alnum = letter or digit

    public static function regexpSearch($field, $query)
    {
        $query = mb_strtolower($query);

        $regexp = "[[:alpha:] -]*";

        $result = "";
        $result .= "`";
        $result .= "REGEXP '\"{$field}\":\"{$regexp}{$query}{$regexp}'";
        $result .= "`";

        return $result;
    }

    // LIKE '%$value%'
    public static function like($value): string
    {
        $result = "";
        $result .= "`";
        $result .= "LIKE ";
        $result .= "'%";
        $result .= $value;
        $result .= "%'";
        $result .= "`";

        return $result;
    }

    public function truncateTable($table)
    {
        global $dbh;

        // Forcing truncate
        $sql = "SET FOREIGN_KEY_CHECKS = 0;";
        $sql .= "TRUNCATE TABLE $table";

        // $sql = "TRUNCATE TABLE $table";

        try {

            $dbh->exec($sql);
            //echo "Table '$table' trucated successfully!";

        } catch (PDOException $e) {

            echo $e->getMessage();
        }
    }

    public static function n($query)
    {
        return $query . "\n";
    }

    public static function showTable($table)
    {
        global $dbh;

        $sql = "SELECT * FROM $table";

        try {
            echo "<br>";
            echo "Showing content of table '$table'";
            echo "<br><br>";

            $results = $dbh->query($sql);
            echo "<pre>";
            var_dump($results->fetchAll(PDO::FETCH_ASSOC));
            echo "</pre>";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function e($sql, $type = 'fetch')
    {
        global $dbh;

        try {
            $results = $dbh->query($sql);
            if ($type == 'fetch') {
                return $results->fetchAll(PDO::FETCH_ASSOC);
            }
            if ($type == 'count') {
                return $results->rowCount();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    // #todo: transfer to the new class Mysql

    public static function ts()
    {
        return '`UTC_TIMESTAMP()`';
    }
}
