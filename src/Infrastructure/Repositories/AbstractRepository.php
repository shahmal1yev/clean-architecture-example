<?php

namespace Onion\Infrastructure\Repositories;

use Onion\Infrastructure\Exceptions\PDOException;
use PDO;

final readonly class AbstractRepository
{
    public function __construct(private PDO $pdo, private string $tableName)
    {
    }

    public function findById(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM public.{$this->tableName} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            PDOException::throw($this->pdo);
        }

        return $data;
    }

    public function save(array $data): false|string
    {
        $columns = implode(', ', array_keys($data));
        $params = implode(", ", array_map(fn(string $name) => ":$name", array_keys($data)));

        $query = "INSERT INTO public.{$this->tableName}($columns) VALUES ($params)";
        $stmt = $this->pdo->prepare($query);

        if (false === $stmt) {
            PDOException::throw($this->pdo);
        }

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $executionResult = $stmt->execute();

        if (false === $executionResult) {
            PDOException::throw($this->pdo);
        }

        return $this->pdo->lastInsertId();
    }
}
