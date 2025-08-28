<?php

namespace Tests\Unit\Services;

use Onion\App\Services\BookService;
use Onion\Domain\Entities\Book;
use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Domain\Services\TransactionManagerInterface;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Builders\BookBuilder;
use Tests\TestCase;

#[CoversClass(BookService::class)]
class BookServiceTest extends TestCase
{
    private readonly BookService $service;
    private BookRepositoryInterface $mockRepository;
    private TransactionManagerInterface $mockTransactionManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = $this->createMock(BookRepositoryInterface::class);
        $this->mockTransactionManager = $this->createMock(TransactionManagerInterface::class);
        
        // Mock transactional method to execute callback immediately
        $this->mockTransactionManager
            ->method('transactional')
            ->willReturnCallback(function (callable $operation) {
                return $operation();
            });
            
        $this->service = new BookService($this->mockRepository, $this->mockTransactionManager);
    }

    #[Test]
    public function it_creates_book_successfully(): void
    {
        $expectedBook = BookBuilder::create()
            ->withId(1)
            ->withName('Lord of Rings')
            ->withAuthor('J. R. Tolkien')
            ->withTimestamps()
            ->build();

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Book $book): bool {
                return $book->getName() === 'Lord of Rings'
                    && $book->getAuthor() === 'J. R. Tolkien'
                    && $book->getId() === null
                    && $book->getCreatedAt() === null
                    && $book->getUpdatedAt() === null;
            }))
            ->willReturn($expectedBook);

        $result = $this->service->create('Lord of Rings', 'J. R. Tolkien');

        $this->assertInstanceOf(Book::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('Lord of Rings', $result->getName());
        $this->assertSame('J. R. Tolkien', $result->getAuthor());
    }

    #[Test]
    public function it_handles_empty_book_name(): void
    {
        $expectedBook = BookBuilder::create()
            ->withId(2)
            ->withName('')
            ->withAuthor('Anonymous')
            ->build();

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($expectedBook);

        $result = $this->service->create('', 'Anonymous');

        $this->assertSame('', $result->getName());
        $this->assertSame('Anonymous', $result->getAuthor());
    }

    #[Test]
    public function it_handles_empty_author_name(): void
    {
        $expectedBook = BookBuilder::create()
            ->withId(3)
            ->withName('Mystery Book')
            ->withAuthor('')
            ->build();

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($expectedBook);

        $result = $this->service->create('Mystery Book', '');

        $this->assertSame('Mystery Book', $result->getName());
        $this->assertSame('', $result->getAuthor());
    }

    #[Test]
    public function it_propagates_repository_exceptions(): void
    {
        $exception = new PDOException('Database connection failed');

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->expectException(BookCreationFailedException::class);
        $this->expectExceptionMessage('Book could not be created');

        $this->service->create('Test Book', 'Test Author');
    }

    #[Test]
    public function it_handles_special_characters_in_input(): void
    {
        $specialName = "Book with 'quotes' and \"double quotes\"";
        $specialAuthor = "Author with ñ, é, and 中文";

        $expectedBook = BookBuilder::create()
            ->withId(4)
            ->withName($specialName)
            ->withAuthor($specialAuthor)
            ->build();

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($expectedBook);

        $result = $this->service->create($specialName, $specialAuthor);

        $this->assertSame($specialName, $result->getName());
        $this->assertSame($specialAuthor, $result->getAuthor());
    }

    #[Test]
    public function it_handles_very_long_input_strings(): void
    {
        $longName = str_repeat('Very Long Book Title ', 100);
        $longAuthor = str_repeat('Very Long Author Name ', 50);

        $expectedBook = BookBuilder::create()
            ->withId(5)
            ->withName($longName)
            ->withAuthor($longAuthor)
            ->build();

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($expectedBook);

        $result = $this->service->create($longName, $longAuthor);

        $this->assertSame($longName, $result->getName());
        $this->assertSame($longAuthor, $result->getAuthor());
    }
}
