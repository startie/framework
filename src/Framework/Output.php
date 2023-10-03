<?php

namespace Startie;

class Output
{
    public static function json(any $data): string
    {
        return json_encode($data);
    }

    public static function plain(string $data): string
    {
        return $data;
    }
}
