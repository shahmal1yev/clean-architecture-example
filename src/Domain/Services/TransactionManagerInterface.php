<?php

namespace Onion\Domain\Services;

/**
 * Transaction Manager Interface
 *
 * Provides abstraction for transaction management in the Domain layer.
 * This interface allows Application Services to control transactional boundaries
 * without coupling to specific database implementation details.
 */
interface TransactionManagerInterface
{
    /**
     * Begin a database transaction
     *
     * @throws \Exception If transaction cannot be started
     */
    public function beginTransaction(): void;

    /**
     * Commit the current transaction
     *
     * @throws \Exception If transaction cannot be committed
     */
    public function commit(): void;

    /**
     * Rollback the current transaction
     *
     * @throws \Exception If transaction cannot be rolled back
     */
    public function rollback(): void;

    /**
     * Execute a callable within a transaction
     *
     * This method provides a convenient way to execute operations within a transaction.
     * If the callable throws an exception, the transaction will be rolled back.
     * If the callable executes successfully, the transaction will be committed.
     *
     * @template T
     * @param callable(): T $operation The operation to execute within the transaction
     * @return T The result of the operation
     * @throws \Exception If the operation fails or transaction management fails
     */
    public function transactional(callable $operation): mixed;

    /**
     * Check if a transaction is currently active
     */
    public function inTransaction(): bool;
}
