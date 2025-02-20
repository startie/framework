<?php

namespace Startie;

class Storage
{
    /**
     * @throws \PDOException
     */
    public static function pdo(string $dsn, string $texts): \PDO
    {
        try {
            $pdo = new \PDO($dsn);
            return $pdo;
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}