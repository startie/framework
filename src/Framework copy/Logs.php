<?php

namespace Startie;

use Startie\Model;
use Startie\Cookie;
use Startie\Schema;

class Logs
{
    public static $types = [
        ['id' => 'errors', 'name' => 'errors'],
        ['id' => 'actions', 'name' => 'actions'],
    ];

    public static $objects = [
        ['id' => 'db', 'name' => 'db'],
        ['id' => 'php', 'name' => 'php'],
        ['id' => 'vk', 'name' => 'vk'],
        ['id' => 'security', 'name' => 'security'],
    ];

    public static function config()
    {
        $stage = strtolower(Config::$stage);
        $machine = strtolower(Config::$machine);

        $Config_Logs_path = App::path("backend/Config/Logs/{$stage}_{$machine}.php");
        $Config_Logs = require $Config_Logs_path;

        $LogsStorageType = $Config_Logs['storage']['type'];

        if (strtolower($LogsStorageType) == "db") {
            $Config_Db_path = App::path("backend/Config/Db/Common.php");
            $Config_Db = require $Config_Db_path;
        } else {
            die('Unknown storage type for logs');
        }

        $dsn = Storage::dsn($Config_Db['logs']);
        if (isset($Config_Db['logs']['texts'])) {
            $texts = $Config_Db['logs']['texts'];
        } else {
            $texts = '';
        };

        return [
            'storage' =>
            compact(
                'dsn',
                'texts'
            )
        ];
    }

    /**
     * Creates table for logs
     *
     * @return void
     */

    public static function up()
    {
        $Config_Logs = Logs::config();
        $dsn = $Config_Logs['storage']['dsn'];
        $texts = $Config_Logs['storage']['texts'];

        $pdo = Storage::pdo($dsn, $texts);

        $pdo->query("
            CREATE TABLE IF NOT EXISTS `Logs` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
                `UserId` int DEFAULT NULL,
                `line` int DEFAULT NULL,
                `file` varchar(1000) DEFAULT NULL,
                `url` varchar(1000) DEFAULT NULL,
                `message` mediumtext,
                `trace` text,
                `type` varchar(10) DEFAULT NULL,
                `object` varchar(10) DEFAULT NULL,
                `isHidden` int DEFAULT NULL
            );
        ");
    }

    /**
     * Generate record in DB from exception
     *
     * @param  array $data
     * @return int
     */
    public static function generate($data)
    {
        // Generate table
        self::up();

        extract($data);

        // Connect
        $Config_Logs = Logs::config();
        $dsn = $Config_Logs['storage']['dsn'];
        $texts = $Config_Logs['storage']['texts'];
        $pdo = Storage::pdo($dsn, $texts);

        // Store
        $sql = "
            INSERT INTO Logs 
            (`UserId`, `line`, `file`, `url`, `message`, `trace`, `type`, `object`) 
            VALUES 
            (:UserIdCurrent, :line, :file, :url, :message, :trace, :type, :object) 
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        return intval(
            $pdo->lastInsertId()
        );
    }

    public static function select()
    {
        $timezoneOffset = Cookie::get('timezoneOffset', 'INT');
        if (!$timezoneOffset) {
            $timezoneOffset = $_ENV['TIMEZONE_OFFSET'];
        }

        $select = [
            '*',
            "DATE_ADD(createdAt, INTERVAL $timezoneOffset MINUTE) as createdAt"
        ];

        return $select;
    }
}
