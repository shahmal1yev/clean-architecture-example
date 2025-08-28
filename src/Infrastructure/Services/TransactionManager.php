<?php

namespace Onion\Infrastructure\Services;

use Onion\Domain\Services\TransactionManagerInterface;
use PDO;
use PDOException;

/**
 * PDO-based Transaction Manager Implementation
 * 
 * This class implements transaction management using PDO for PostgreSQL database.
 * It provides a clean abstraction for transaction control while handling
 * PDO-specific error conditions and edge cases.
 */
readonly class TransactionManager implements TransactionManagerInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function beginTransaction(): void
    {
        if ($this->inTransaction()) {
            throw new \RuntimeException('Transaction is already active');
        }

        try {
            if (!$this->pdo->beginTransaction()) {
                throw new \RuntimeException('Failed to begin transaction');
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to begin transaction: ' . $e->getMessage(), 0, $e);
        }
    }

    public function commit(): void
    {
        if (!$this->inTransaction()) {
            throw new \RuntimeException('No active transaction to commit');
        }

        try {
            if (!$this->pdo->commit()) {
                throw new \RuntimeException('Failed to commit transaction');
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to commit transaction: ' . $e->getMessage(), 0, $e);
        }
    }

    public function rollback(): void
    {
        if (!$this->inTransaction()) {
            throw new \RuntimeException('No active transaction to rollback');
        }

        try {
            if (!$this->pdo->rollBack()) {
                throw new \RuntimeException('Failed to rollback transaction');
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to rollback transaction: ' . $e->getMessage(), 0, $e);
        }
    }

    public function transactional(callable $operation): mixed
    {
        $this->beginTransaction();

        try {
            $result = $operation();
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            try {
                $this->rollback();
            } catch (\Throwable $rollbackException) {
                // Log rollback failure but throw the original exception
                error_log('Failed to rollback transaction: ' . $rollbackException->getMessage());
            }
            throw $e;
        }
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}