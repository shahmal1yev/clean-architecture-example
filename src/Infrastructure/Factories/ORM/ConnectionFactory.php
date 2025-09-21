<?php

namespace Onion\Infrastructure\Factories\ORM;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;

final class ConnectionFactory
{
    private static array $loadedParams;

    public function __construct(private readonly Configuration $config, private ?array $params = null)
    {
    }

    public function create(): \Doctrine\DBAL\Connection
    {
        if (is_null($this->params)) {
            $this->params = self::$loadedParams ??= require __DIR__ . "/../../../../migrations-db.php";
        }

        return DriverManager::getConnection($this->params, $this->config);
    }
}
