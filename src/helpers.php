<?php

use Startie\Dump;

function dd($result, $msg, $trace)
{
    Dump::made($result, $msg, $trace);
}