<?php

namespace App\Helpers;

class DtrToken
{
    public static function generate(): string
    {
        $secret = config('dtr.access_token');

        return hash('sha256', $secret . date('Y-m-d H'));
    }
}
