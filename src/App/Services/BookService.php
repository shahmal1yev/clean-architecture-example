<?php

namespace Onion\App\Services;

use Onion\Domain\Entities\Book;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Repositories\BookRepository;

readonly class BookService
{

    public function __construct(private BookRepositoryInterface $repository)
    {
    }

    public function create(string $name, string $author): Book
    {
        return $this->repository->save(new Book(
            id: null,
            name: $name,
            author: $author,
            createdAt: null,
            updatedAt: null
        ));
    }
}
