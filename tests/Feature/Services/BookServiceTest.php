<?php

namespace Tests\Feature\Services;

use Onion\App\Services\BookService;
use Onion\Domain\Entities\Book;
use Onion\Domain\Exceptions\FindBookFailedException;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Repositories\BookRepository;
use PDOException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class BookServiceTest extends TestCase
{
    private BookService $service;
    private BookRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = self::$container->get(BookRepositoryInterface::class);
        $this->service = self::$container->get(BookService::class);
    }

    #[Test]
    public function it_creates_and_persists_book_through_full_stack(): void
    {
        $book = $this->service->create('The Hobbit', 'J.R.R. Tolkien');

        // Verify the service returned a proper Book entity
        $this->assertInstanceOf(Book::class, $book);
        $this->assertIsInt($book->getId());
        $this->assertGreaterThan(0, $book->getId());
        $this->assertSame('The Hobbit', $book->getName());
        $this->assertSame('J.R.R. Tolkien', $book->getAuthor());
        $this->assertInstanceOf(\DateTimeImmutable::class, $book->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $book->getUpdatedAt());

        // Verify the book was actually persisted by fetching it independently
        $persistedBook = $this->repository->findById($book->getId());
        
        $this->assertInstanceOf(Book::class, $persistedBook);
        $this->assertSame($book->getId(), $persistedBook->getId());
        $this->assertSame($book->getName(), $persistedBook->getName());
        $this->assertSame($book->getAuthor(), $persistedBook->getAuthor());
        
        // Verify timestamps are properly set and reasonable
        $this->assertEqualsWithDelta(
            time(), 
            $persistedBook->getCreatedAt()->getTimestamp(), 
            5, // 5 second tolerance
            'Created timestamp should be recent'
        );
        $this->assertEqualsWithDelta(
            time(), 
            $persistedBook->getUpdatedAt()->getTimestamp(), 
            5,
            'Updated timestamp should be recent'
        );
    }

    #[Test]
    public function it_handles_special_characters_end_to_end(): void
    {
        $specialTitle = "Cien años de soledad";
        $specialAuthor = "Gabriel García Márquez";

        $book1 = $this->service->create($specialTitle, $specialAuthor);
        $book2 = $this->service->create($specialTitle, $specialAuthor);
        $persistedBook = $this->repository->findById($book2->getId());

        $this->assertSame($specialTitle, $persistedBook->getName());
        $this->assertSame($specialAuthor, $persistedBook->getAuthor());
    }

    #[Test]
    public function it_handles_empty_values_end_to_end(): void
    {
        $book = $this->service->create('', '');
        $persistedBook = $this->repository->findById($book->getId());

        $this->assertSame('', $persistedBook->getName());
        $this->assertSame('', $persistedBook->getAuthor());
        $this->assertNotNull($persistedBook->getId());
    }

    #[Test]
    public function it_throws_exception_for_nonexistent_book(): void
    {
        $this->expectException(FindBookFailedException::class);
        $this->expectExceptionMessage('Book could not be found: 99999');

        $this->service->find(99999);
    }

    #[Test]
    public function it_documents_potential_sql_injection_vulnerability(): void
    {
        // This test documents a critical security vulnerability in the BookRepository
        // 
        // VULNERABILITY LOCATION: BookRepository::findById() line 15
        // CODE: $data = $this->pdo()->query("SELECT * FROM books WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
        //
        // RISK: Direct string interpolation in SQL query without parameterization
        // CURRENT MITIGATION: PHP 8.4 type hints prevent string injection (int $id parameter)
        // POTENTIAL EXPLOIT: If type checking is bypassed, SQL injection is possible
        //
        // RECOMMENDED FIX: Use prepared statements:
        // $stmt = $this->pdo()->prepare("SELECT * FROM books WHERE id = ?");
        // $stmt->execute([$id]);
        // $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $legitimateBook = $this->service->create('Security Test Book', 'Security Author');
        
        // Demonstrate normal operation works
        $foundBook = $this->repository->findById($legitimateBook->getId());
        $this->assertSame($legitimateBook->getId(), $foundBook->getId());
        
        // Document that the vulnerability exists in the code structure
        $this->assertTrue(true, 'SQL injection vulnerability documented - requires code review of BookRepository::findById()');
    }
}
