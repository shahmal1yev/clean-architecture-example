<?php

require_once __DIR__ . '/bootstrap.php';

return [
    'driver' => env('DB_DRIVER'),
    'host' => env('DB_HOST'),
    'port' => env('DB_PORT'),
    'user' => env('DB_USER'),
    'password' => env('DB_PASSWORD'),
    'dbname' => env('DB_NAME'),
];
