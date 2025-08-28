<?php

namespace Tests\Unit\Infrastructure\Services;

use Onion\Infrastructure\Services\TransactionManager;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TransactionManagerTest extends TestCase
{
    private PDO&MockObject $pdoMock;
    private TransactionManager $transactionManager;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->transactionManager = new TransactionManager($this->pdoMock);
    }

    public function testBeginTransactionSuccess(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(false);

        $this->pdoMock
            ->expects($this->once())
            ->method('beginTransaction')
            ->willReturn(true);

        $this->transactionManager->beginTransaction();
    }

    public function testBeginTransactionThrowsExceptionWhenAlreadyInTransaction(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(true);

        $this->pdoMock
            ->expects($this->never())
            ->method('beginTransaction');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Transaction is already active');

        $this->transactionManager->beginTransaction();
    }

    public function testBeginTransactionThrowsExceptionOnPDOFailure(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(false);

        $this->pdoMock
            ->expects($this->once())
            ->method('beginTransaction')
            ->willReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to begin transaction');

        $this->transactionManager->beginTransaction();
    }

    public function testCommitSuccess(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(true);

        $this->pdoMock
            ->expects($this->once())
            ->method('commit')
            ->willReturn(true);

        $this->transactionManager->commit();
    }

    public function testCommitThrowsExceptionWhenNoActiveTransaction(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(false);

        $this->pdoMock
            ->expects($this->never())
            ->method('commit');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No active transaction to commit');

        $this->transactionManager->commit();
    }

    public function testRollbackSuccess(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(true);

        $this->pdoMock
            ->expects($this->once())
            ->method('rollBack')
            ->willReturn(true);

        $this->transactionManager->rollback();
    }

    public function testRollbackThrowsExceptionWhenNoActiveTransaction(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(false);

        $this->pdoMock
            ->expects($this->never())
            ->method('rollBack');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No active transaction to rollback');

        $this->transactionManager->rollback();
    }

    public function testTransactionalSuccess(): void
    {
        $expectedResult = 'test result';
        
        $this->pdoMock
            ->expects($this->exactly(2))
            ->method('inTransaction')
            ->willReturn(false, true); // First call for beginTransaction check, second for commit check

        $this->pdoMock
            ->expects($this->once())
            ->method('beginTransaction')
            ->willReturn(true);

        $this->pdoMock
            ->expects($this->once())
            ->method('commit')
            ->willReturn(true);

        $this->pdoMock
            ->expects($this->never())
            ->method('rollBack');

        $operation = fn() => $expectedResult;

        $result = $this->transactionManager->transactional($operation);

        $this->assertEquals($expectedResult, $result);
    }

    public function testTransactionalRollsBackOnException(): void
    {
        $expectedException = new \Exception('Operation failed');

        $this->pdoMock
            ->expects($this->exactly(2))
            ->method('inTransaction')
            ->willReturn(false, true); // First call for beginTransaction check, second for rollback check

        $this->pdoMock
            ->expects($this->once())
            ->method('beginTransaction')
            ->willReturn(true);

        $this->pdoMock
            ->expects($this->never())
            ->method('commit');

        $this->pdoMock
            ->expects($this->once())
            ->method('rollBack')
            ->willReturn(true);

        $operation = function () use ($expectedException) {
            throw $expectedException;
        };

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Operation failed');

        $this->transactionManager->transactional($operation);
    }

    public function testInTransaction(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(true);

        $this->assertTrue($this->transactionManager->inTransaction());
    }

    public function testNotInTransaction(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('inTransaction')
            ->willReturn(false);

        $this->assertFalse($this->transactionManager->inTransaction());
    }
}