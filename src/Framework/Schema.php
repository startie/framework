<?php

namespace Startie;

use MySql;

class Schema
{
    /**
     * @deprecated 0.30.0 Use `Sql::hasBackticks()`
     */
    public static function hasBackticks($expression)
    {
        return Sql::startsWithBacktick($expression);
    }

    // TODO: transfer to the new class Mysql
    // TODO: rename to jsonRegExp
    // since 5.6
    // alnum = letter or digit
    public static function regexpSearch(string $field, string $query): string
    {
        $query = mb_strtolower($query);

        $regexp = "[[:alpha:] -]*";

        $result = "";
        $result .= "`";
        $result .= "REGEXP '\"{$field}\":\"{$regexp}{$query}{$regexp}'";
        $result .= "`";

        return $result;
    }

    /**
     * @deprecated 0.30.0 Use `Sql::like()`
     */
    public static function like($value): string
    {
        return Sql::like($value);
    }

    public function truncateTable(string $table): void
    {
        global $dbh;

        // Forcing truncate
        $sql = "SET FOREIGN_KEY_CHECKS = 0;";
        $sql .= "TRUNCATE TABLE $table";

        try {
            $dbh->exec($sql);
            // echo "Table '$table' trucated successfully!";
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function n(string $query): string
    {
        return $query . "\n";
    }

    public static function showTable(string $table): void
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

    public static function e(string $sql, string $type = 'fetch'): mixed
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

    /**
     * @deprecated 0.30.0 Use Sql::ts()
     */
    public static function ts(): string
    {
        return Sql::ts();
    }
}