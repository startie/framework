<?php

namespace Startie;

use PDOException;

class Exception extends \Exception
{
    public string $object;
    public string $type;

    public function __construct(
        string $message,
        string $type = "error",
        string $object = "php",
        int|string $code = 0,
        \Throwable|null $previous = NULL
    ) {
        parent::__construct($message, intval($code), $previous);

        $this->type = $type;
        $this->object = $object;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * Returns an exception based on PDOException
     * 
     * ```php
     * throw \Startie\Exception::create($e);
     * ```
     *
     * @param  mixed $e
     * @return \Startie\Exception
     */
    public static function create(
        PDOException $e,
        string $sql
    ): \Startie\Exception {
        $message = $e->getMessage()
            . ". "
            . $sql;

        $exception = new Exception(
            $message,
            "error",
            "db",
            $e->getCode(),
            $e->getPrevious()
        );

        return $exception;
    }
}