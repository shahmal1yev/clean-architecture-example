<?php

namespace Onion\Infrastructure\Repositories;

use Onion\Infrastructure\Exceptions\PDOException;
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


    public function abstractFindById($id): array
    {
        $tableName = $this->tableName();
        $stmt = $this->pdo()->prepare("SELECT * FROM public.$tableName WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            PDOException::throw($this->pdo());
        }

        return $data;
    }

    public function abstractSave(array $data): false|string
    {
        $columns = implode(', ', array_keys($data));
        $params = implode(", ", array_map(fn(string $name) => ":$name", array_keys($data)));
        $tableName = $this->tableName();

        $query = "INSERT INTO public.$tableName($columns) VALUES ($params)";
        $stmt = $this->pdo()->prepare($query);

        if ($stmt === false) {
            PDOException::throw($this->pdo());
        }

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $executionResult = $stmt->execute();

        if ($executionResult === false) {
            PDOException::throw($this->pdo());
        }

        return $this->pdo()->lastInsertId();
    }

    abstract public function tableName(): string;
}
