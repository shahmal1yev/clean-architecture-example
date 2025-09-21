<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\Repositories\BookRepository;

use Onion\Domain\Entities\BookInterface;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Entities\Book;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{

    private BookRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = container()->get(BookRepositoryInterface::class);
    }

    public function test_create_returns_book_instance(): void
    {
        $book = new Book('Title', 'Author', 'Description');

        $this->assertNull($book->id);

        $actual = $this->repository->create($book);

        $this->assertInstanceOf(BookInterface::class, $actual);
        $this->assertIsInt($actual->id);
        $this->assertNotNull($this->repository->findById($actual->id));

    }
}
