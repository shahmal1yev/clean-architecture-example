<?php

namespace Onion\Infrastructure\Repositories;

use PDO;

abstract readonly class Repository
{
    public function __construct(private PDO $pdo)
    {
    }

    protected function pdo(): PDO
    {
        return $this->pdo;
    }
}
