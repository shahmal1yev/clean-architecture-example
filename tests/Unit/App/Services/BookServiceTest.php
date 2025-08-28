<?php

namespace Tests\Unit\App\Services;

use Onion\App\Services\BookService;
use Onion\Domain\Entities\Book;
use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Domain\Exceptions\FindBookFailedException;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Domain\Services\TransactionManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BookServiceTest extends TestCase
{
    private BookRepositoryInterface&MockObject $repositoryMock;
    private TransactionManagerInterface&MockObject $transactionManagerMock;
    private BookService $bookService;

    public function setUp(): void
    {
        $this->repositoryMock = $this->createMock(BookRepositoryInterface::class);
        $this->transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
        
        $this->bookService = new BookService(
            $this->repositoryMock,
            $this->transactionManagerMock
        );
    }

    public function testCreateBookSuccess(): void
    {
        $name = 'Test Book';
        $author = 'Test Author';
        
        $expectedBook = new Book(
            id: 1,
            name: $name,
            author: $author,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );

        // Mock the transactional method to execute the callback immediately
        $this->transactionManagerMock
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $operation) {
                return $operation();
            });

        $this->repositoryMock
            ->expects($this->once())
            ->method('save')
            ->willReturn($expectedBook);

        $result = $this->bookService->create($name, $author);

        $this->assertEquals($expectedBook, $result);
    }

    public function testCreateBookThrowsExceptionOnRepositoryFailure(): void
    {
        $name = 'Test Book';
        $author = 'Test Author';
        
        $repositoryException = new \Exception('Database error');

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $operation) {
                return $operation();
            });

        $this->repositoryMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException($repositoryException);

        $this->expectException(BookCreationFailedException::class);
        $this->expectExceptionMessage('Book could not be created');

        $this->bookService->create($name, $author);
    }

    public function testCreateBookThrowsExceptionOnTransactionFailure(): void
    {
        $name = 'Test Book';
        $author = 'Test Author';
        
        $transactionException = new \RuntimeException('Transaction failed');

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('transactional')
            ->willThrowException($transactionException);

        $this->repositoryMock
            ->expects($this->never())
            ->method('save');

        $this->expectException(BookCreationFailedException::class);
        $this->expectExceptionMessage('Book could not be created');

        $this->bookService->create($name, $author);
    }

    public function testFindBookSuccess(): void
    {
        $bookId = 1;
        
        $expectedBook = new Book(
            id: $bookId,
            name: 'Test Book',
            author: 'Test Author',
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($bookId)
            ->willReturn($expectedBook);

        $result = $this->bookService->find($bookId);

        $this->assertEquals($expectedBook, $result);
    }

    public function testFindBookThrowsExceptionOnRepositoryFailure(): void
    {
        $bookId = 1;
        $repositoryException = new \Exception('Database error');

        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($bookId)
            ->willThrowException($repositoryException);

        $this->expectException(FindBookFailedException::class);
        $this->expectExceptionMessage("Book could not be found: $bookId");

        $this->bookService->find($bookId);
    }

    public function testCreateMultipleBooksSuccess(): void
    {
        $bookData = [
            ['name' => 'Book 1', 'author' => 'Author 1'],
            ['name' => 'Book 2', 'author' => 'Author 2']
        ];

        $expectedBooks = [
            new Book(1, 'Book 1', 'Author 1', new \DateTimeImmutable(), new \DateTimeImmutable()),
            new Book(2, 'Book 2', 'Author 2', new \DateTimeImmutable(), new \DateTimeImmutable())
        ];

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $operation) {
                return $operation();
            });

        $this->repositoryMock
            ->expects($this->exactly(2))
            ->method('save')
            ->willReturnOnConsecutiveCalls($expectedBooks[0], $expectedBooks[1]);

        $result = $this->bookService->createMultipleBooks($bookData);

        $this->assertEquals($expectedBooks, $result);
    }

    public function testCreateMultipleBooksRollsBackOnFailure(): void
    {
        $bookData = [
            ['name' => 'Book 1', 'author' => 'Author 1'],
            ['name' => 'Book 2', 'author' => 'Author 2']
        ];

        $expectedBook1 = new Book(1, 'Book 1', 'Author 1', new \DateTimeImmutable(), new \DateTimeImmutable());
        $repositoryException = new \Exception('Database error on second book');

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $operation) {
                return $operation();
            });

        $this->repositoryMock
            ->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(function () use ($expectedBook1, $repositoryException) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount === 1) {
                    return $expectedBook1;
                }
                
                throw $repositoryException;
            });

        $this->expectException(BookCreationFailedException::class);
        $this->expectExceptionMessage('Multiple books could not be created');

        $this->bookService->createMultipleBooks($bookData);
    }
}