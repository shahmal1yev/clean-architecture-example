<?php

namespace Tests\Unit\Presentation\Adapters;

use Onion\App\Services\BookService;
use Onion\Domain\Entities\Book;
use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Domain\Exceptions\FindBookFailedException;
use Onion\Presentation\Adapters\GraphBookManagementAdapter;
use PHPUnit\Framework\TestCase;

class GraphBookManagementAdapterTest extends TestCase
{
    private BookService $bookService;
    private GraphBookManagementAdapter $adapter;

    public function setUp(): void
    {
        $this->bookService = $this->createMock(BookService::class);
        $this->adapter = new GraphBookManagementAdapter($this->bookService);
    }

    public function test_createBook_success(): void
    {
        $bookData = ['name' => 'Test Book', 'author' => 'Test Author'];
        $expectedBook = new Book(1, 'Test Book', 'Test Author');
        
        $this->bookService
            ->expects($this->once())
            ->method('create')
            ->with('Test Book', 'Test Author')
            ->willReturn($expectedBook);

        $result = $this->adapter->createBook($bookData);

        $this->assertSame($expectedBook, $result);
    }

    public function test_createBook_validation_failure(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Book name is required and must be a non-empty string');

        $this->adapter->createBook(['author' => 'Test Author']);
    }

    public function test_findBook_success(): void
    {
        $expectedBook = new Book(1, 'Test Book', 'Test Author');
        
        $this->bookService
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($expectedBook);

        $result = $this->adapter->findBook(1);

        $this->assertSame($expectedBook, $result);
    }

    public function test_findBook_invalid_id(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Book ID must be a positive integer');

        $this->adapter->findBook(0);
    }

    public function test_createMultipleBooks_success(): void
    {
        $booksData = [
            ['name' => 'Book 1', 'author' => 'Author 1'],
            ['name' => 'Book 2', 'author' => 'Author 2']
        ];
        
        $expectedBooks = [
            new Book(1, 'Book 1', 'Author 1'),
            new Book(2, 'Book 2', 'Author 2')
        ];

        $this->bookService
            ->expects($this->once())
            ->method('createMultipleBooks')
            ->with($booksData)
            ->willReturn($expectedBooks);

        $result = $this->adapter->createMultipleBooks($booksData);

        $this->assertSame($expectedBooks, $result);
    }

    public function test_createMultipleBooks_empty_data(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one book must be provided');

        $this->adapter->createMultipleBooks([]);
    }

    public function test_createMultipleBooks_validation_failure_at_index(): void
    {
        $booksData = [
            ['name' => 'Valid Book', 'author' => 'Valid Author'],
            ['author' => 'Missing Name']
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Book at index 1: Book name is required and must be a non-empty string');

        $this->adapter->createMultipleBooks($booksData);
    }
}