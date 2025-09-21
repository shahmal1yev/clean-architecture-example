<?php

namespace Tests\Feature\Infrastructure\Repositories\BookRepository;

use Onion\Domain\Entities\BookInterface;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Entities\Book;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    private BookRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = container()->get(BookRepositoryInterface::class);
    }

    public function test_it_should_find_book(): void
    {
        $book = new Book('Title 1', 'Author 1', 'Description 1');
        $actual = $this->repository->create($book);

        $this->assertInstanceOf(BookInterface::class, $actual);
        $this->assertIsInt($actual->id);
        $this->assertInstanceOf(BookInterface::class, $this->repository->findById($actual->id));
    }
}
