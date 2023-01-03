<?php

namespace Startie;

class Storage
{
    /**
     * Returns dsn
     *
     * @param  array $Config_Db
     * @return string
     */
    public static function dsn(array $Config_Db)
    {
        $dsn = "";

        extract($Config_Db);

        switch ($driver) {
            case 'sqlite':
                $dsn = "sqlite:$path";
                break;

            case 'mysql':
                $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
                break;

            default:
                die("Unable return DSN for this driver: $driver");
        }

        return $dsn;
    }

    public static function pdo($dsn, $texts)
    {
        try {
            $pdo = new \PDO($dsn);
            return $pdo;
        } catch (\PDOException $e) {
            echo $texts['connection_error'];
            die($e->getMessage());
        }
    }
}
