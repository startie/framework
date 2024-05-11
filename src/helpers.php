<?php

use Startie\Dump;

function dd($result = NULL, $msg = NULL, $trace = NULL)
{
    Dump::made($result, $msg, $trace);
}