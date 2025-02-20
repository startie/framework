<?php

namespace Startie;

class Schema
{
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

    /**
     * @psalm-suppress ForbiddenCode
     */
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
            var_dump(
                $results->fetchAll(\PDO::FETCH_ASSOC)
            );
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
                return $results->fetchAll(\PDO::FETCH_ASSOC);
            } elseif ($type == 'count') {
                return $results->rowCount();
            } else {
                throw new \Exception('Unknown type of fetch: `' . $type . '`');
            }
        } catch (Exception $e) {
            throw new $e;
        }
    }

    /**
     * @psalm-suppress all
     * @deprecated 0.30.0 Use Sql::ts()
     */
    public static function ts(): string
    {
        return Sql::ts();
    }

    /**
     * @psalm-suppress all
     * @deprecated 0.30.0 Use `Sql::like()`
     */
    public static function like(string|int $value): string
    {
        return Sql::like($value);
    }

    /**
     * @psalm-suppress all
     * @deprecated 0.30.0 Use `Sql::hasBackticks()`
     */
    public static function hasBackticks(string $expression): bool
    {
        return Sql::startsWithBacktick($expression);
    }
}