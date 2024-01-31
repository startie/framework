<?php

namespace Startie;

use PDOException;

class Exception extends \Exception
{
    public $object;
    public $type;

    public function __construct(
        string $message,
        string $type = "error",
        string $object = "php",
        int $code = 0,
        $previous = NULL
    ) {
        parent::__construct($message, intval($code), $previous);

        $this->type = $type;
        $this->object = $object;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getObject()
    {
        return $this->object;
    }

    /**
     * PDO
     * 
     * Returns an exception based on PDOException
     * 
     * ```php
     * throw \Startie\Exception::PDO($e);
     * ```
     *
     * @param  mixed $e
     * @return \Startie\Exception
     */
    public static function PDO(PDOException $e, $sql)
    {
        $own = new Exception(
            $e->getMessage() . PHP_EOL . PHP_EOL . $sql . PHP_EOL . PHP_EOL,
            "error",
            "db",
            $e->getCode(),
            $e->getPrevious()
        );

        return $own;
    }
}