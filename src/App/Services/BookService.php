<?php

namespace Onion\App\Services;

use Onion\Domain\Entities\Book;
use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Domain\Exceptions\FindBookFailedException;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Domain\Services\TransactionManagerInterface;

readonly class BookService
{

    public function __construct(
        private BookRepositoryInterface $repository,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    public function create(string $name, string $author): Book
    {
        try {
            return $this->transactionManager->transactional(function () use ($name, $author) {
                return $this->repository->save(new Book(
                    name: $name,
                    author: $author,
                    id: null,
                    createdAt: null,
                    updatedAt: null
                ));
            });
        } catch (\Throwable $exception) {
            throw new BookCreationFailedException(
                message: "Book could not be created",
                previous: $exception
            );
        }
    }

    public function find(int $id): Book
    {
        try {
            return $this->repository->findById($id);
        } catch (\Throwable $exception) {
            throw new FindBookFailedException(
                "Book could not be found: $id",
                previous: $exception
            );
        }
    }

    /**
     * Create multiple books in a single transaction
     * 
     * This method demonstrates how to handle multiple operations
     * within a single transaction using the TransactionManager.
     * If any book creation fails, all operations are rolled back.
     * 
     * @param array<array{name: string, author: string}> $bookData
     * @return array<Book>
     * @throws BookCreationFailedException
     */
    public function createMultipleBooks(array $bookData): array
    {
        try {
            return $this->transactionManager->transactional(function () use ($bookData) {
                $createdBooks = [];
                
                foreach ($bookData as $data) {
                    $book = $this->repository->save(new Book(
                        name: $data['name'],
                        author: $data['author'],
                        id: null,
                        createdAt: null,
                        updatedAt: null
                    ));
                    
                    $createdBooks[] = $book;
                }
                
                return $createdBooks;
            });
        } catch (\Throwable $exception) {
            throw new BookCreationFailedException(
                message: "Multiple books could not be created",
                previous: $exception
            );
        }
    }
}
