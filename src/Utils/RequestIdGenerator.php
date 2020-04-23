<?php

namespace App\Utils;

class RequestIdGenerator
{
    private static $requestId;

    static public function generate(): string 
    {
        self::$requestId = random_bytes(8);

        return self::$requestId;
    }
}