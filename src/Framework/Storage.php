<?php

namespace Startie;

class Storage
{
    public static function pdo($dsn, $texts)
    {
        try {
            $pdo = new \PDO($dsn);
            return $pdo;
        } catch (\PDOException $e) {
            echo $texts; // ?
            die($e);
        }
    }
}