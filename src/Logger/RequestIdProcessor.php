<?php

namespace App\Logger;

use App\Utils\RequestIdGenerator;

class RequestIdProcessor
{
    public function __invoke(array $record): array
    {
        $record['extra']['token'] = RequestIdGenerator::generate();

        return $record;
    }
}