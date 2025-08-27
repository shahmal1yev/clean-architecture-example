<?php

namespace Onion\Infrastructure\Factory;

use Onion\Infrastructure\Config\DatabaseCredentials;
use PDO;

readonly class PDOFactory
{
    public function __construct(private DatabaseCredentials $credentials)
    {
    }

    public function create(): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $this->credentials->host,
            $this->credentials->port,
            $this->credentials->database
        );

        return new PDO($dsn, $this->credentials->user, $this->credentials->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }
}
