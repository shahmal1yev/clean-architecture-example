<?php

namespace Onion\Infrastructure\Config;

readonly class DatabaseCredentials
{
    public function __construct(
        private(set) string $host,
        private(set) int    $port = 5432,
        private(set) string $user,
        private(set) string $password,
        private(set) string $database,
    )
    {}
}
