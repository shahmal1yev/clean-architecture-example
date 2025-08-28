<?php

namespace Onion\Infrastructure\Factory\Repositories;

use Onion\Infrastructure\Repositories\AbstractRepository;
use PDO;

final readonly class AbstractRepositoryFactory
{
    public function __construct(private PDO $pdo)
    {}

    public function create(string $tableName): AbstractRepository
    {
        return new AbstractRepository($this->pdo, $tableName);
    }
}
