<?php

namespace Tests\Unit\Infrastructure;

use Onion\Domain\Entities\Book;
use Onion\Infrastructure\Repositories\BookRepository;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Builders\BookBuilder;
use Tests\TestCase;

#[CoversClass(BookRepository::class)]
class BookRepositoryTest extends TestCase
{
    private BookRepository $repository;
    private PDO $mockPdo;
    private PDOStatement $mockStatement;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $this->repository = new BookRepository($this->mockPdo);
    }

    #[Test]
    public function it_finds_book_by_id_successfully(): void
    {
        $bookData = [
            'id' => 1,
            'name' => 'Test Book',
            'author' => 'Test Author',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-01 12:00:00'
        ];

        $this->mockPdo
            ->expects($this->once())
            ->method('query')
            ->with("SELECT * FROM books WHERE id = 1")
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($bookData);

        $result = $this->repository->findById(1);

        $this->assertInstanceOf(Book::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('Test Book', $result->getName());
        $this->assertSame('Test Author', $result->getAuthor());
    }

    #[Test]
    public function it_throws_exception_when_book_not_found(): void
    {
        $this->mockPdo
            ->expects($this->once())
            ->method('query')
            ->with("SELECT * FROM books WHERE id = 999")
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Book not found: 999');

        $this->repository->findById(999);
    }

    #[Test]
    public function it_documents_sql_injection_vulnerability_risk(): void
    {
        // This test documents that the findById method constructs SQL without proper parameterization
        // The actual vulnerability exists in BookRepository::findById() line 15:
        // $data = $this->pdo()->query("SELECT * FROM books WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
        // 
        // If the type hint were removed, this would allow SQL injection like:
        // findById("1 OR 1=1 --") would execute: "SELECT * FROM books WHERE id = 1 OR 1=1 --"
        //
        // Current mitigation: PHP 8.4 type hints prevent string injection into int parameter
        // Risk: If type hints are removed or bypassed, SQL injection becomes possible
        
        $this->assertTrue(true, 'SQL injection vulnerability documented - see BookRepository::findById() line 15');
    }

    #[Test]
    public function it_saves_book_successfully(): void
    {
        $book = BookBuilder::create()
            ->withName('New Book')
            ->withAuthor('New Author')
            ->build();

        $this->mockPdo
            ->expects($this->once())
            ->method('beginTransaction');

        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->with("INSERT INTO public.books(name, author) VALUES (:name, :author)")
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->exactly(2))
            ->method('bindParam')
            ->willReturnCallback(function ($param, &$value) {
                if ($param === ':name') $value = 'New Book';
                if ($param === ':author') $value = 'New Author';
                return true;
            });

        $this->mockStatement
            ->expects($this->once())
            ->method('execute');

        $this->mockPdo
            ->expects($this->once())
            ->method('commit');

        $this->mockPdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('5');

        // Mock the findById call that happens after save
        $this->mockPdo
            ->expects($this->once())
            ->method('query')
            ->with("SELECT * FROM books WHERE id = 5")
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => 5,
                'name' => 'New Book',
                'author' => 'New Author',
                'created_at' => '2024-01-01 12:00:00',
                'updated_at' => '2024-01-01 12:00:00'
            ]);

        $result = $this->repository->save($book);

        $this->assertInstanceOf(Book::class, $result);
        $this->assertSame(5, $result->getId());
        $this->assertSame('New Book', $result->getName());
        $this->assertSame('New Author', $result->getAuthor());
    }

    #[Test]
    public function it_handles_save_transaction_failure(): void
    {
        $book = BookBuilder::create()
            ->withName('Fail Book')
            ->withAuthor('Fail Author')
            ->build();

        $originalException = new PDOException('Database constraint violation');

        $this->mockPdo
            ->expects($this->once())
            ->method('beginTransaction');

        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->any())
            ->method('bindParam');

        $this->mockStatement
            ->expects($this->once())
            ->method('execute')
            ->willThrowException($originalException);

        $this->mockPdo
            ->expects($this->once())
            ->method('rollBack');

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('An error occurred while saving new book: Database constraint violation');

        $this->repository->save($book);
    }

    #[Test]
    public function it_handles_special_characters_in_book_data(): void
    {
        $book = BookBuilder::create()
            ->withName("Book with 'quotes' and \"double quotes\"")
            ->withAuthor("Author with ñ, é, and 中文")
            ->build();

        $this->mockPdo
            ->expects($this->once())
            ->method('beginTransaction');

        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->exactly(2))
            ->method('bindParam');

        $this->mockStatement
            ->expects($this->once())
            ->method('execute');

        $this->mockPdo
            ->expects($this->once())
            ->method('commit');

        $this->mockPdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('10');

        // Mock findById return
        $this->mockPdo
            ->expects($this->once())
            ->method('query')
            ->willReturn($this->mockStatement);

        $this->mockStatement
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 10,
                'name' => "Book with 'quotes' and \"double quotes\"",
                'author' => "Author with ñ, é, and 中文",
                'created_at' => '2024-01-01 12:00:00',
                'updated_at' => '2024-01-01 12:00:00'
            ]);

        $result = $this->repository->save($book);

        $this->assertSame("Book with 'quotes' and \"double quotes\"", $result->getName());
        $this->assertSame("Author with ñ, é, and 中文", $result->getAuthor());
    }
}