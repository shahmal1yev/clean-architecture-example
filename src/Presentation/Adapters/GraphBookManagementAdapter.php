<?php

namespace Onion\Presentation\Adapters;

use Onion\App\Services\BookService;
use Onion\Domain\Entities\Book;
use Onion\Presentation\Ports\BookManagementPort;

readonly class GraphBookManagementAdapter implements BookManagementPort
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    public function createBook(array $bookData): Book
    {
        $this->validateSingleBookData($bookData);
        
        return $this->bookService->create(
            name: trim($bookData['name']),
            author: trim($bookData['author'])
        );
    }

    public function findBook(int $id): Book
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Book ID must be a positive integer');
        }
        
        return $this->bookService->find($id);
    }

    public function createMultipleBooks(array $booksData): array
    {
        if (empty($booksData)) {
            throw new \InvalidArgumentException('At least one book must be provided');
        }
        
        $validatedBooks = [];
        foreach ($booksData as $index => $bookData) {
            try {
                $this->validateSingleBookData($bookData);
                $validatedBooks[] = [
                    'name' => trim($bookData['name']),
                    'author' => trim($bookData['author'])
                ];
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Book at index $index: " . $e->getMessage());
            }
        }
        
        return $this->bookService->createMultipleBooks($validatedBooks);
    }

    private function validateSingleBookData(array $bookData): void
    {
        if (!isset($bookData['name']) || !is_string($bookData['name']) || empty(trim($bookData['name']))) {
            throw new \InvalidArgumentException('Book name is required and must be a non-empty string');
        }
        
        if (!isset($bookData['author']) || !is_string($bookData['author']) || empty(trim($bookData['author']))) {
            throw new \InvalidArgumentException('Author is required and must be a non-empty string');
        }
    }
}
