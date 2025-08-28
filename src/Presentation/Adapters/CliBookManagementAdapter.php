<?php

namespace Onion\Presentation\Adapters;

use Onion\App\Services\BookService;
use Onion\Domain\Entities\Book;
use Onion\Presentation\Ports\BookManagementPort;

/**
 * CLI Adapter for Book Management
 * 
 * This adapter demonstrates how the same Port interface
 * can be implemented differently for different presentation
 * contexts (HTTP vs CLI). The validation rules might differ
 * based on the input context.
 */
readonly class CliBookManagementAdapter implements BookManagementPort
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    public function createBook(array $bookData): Book
    {
        // CLI might be more lenient with validation
        $this->validateCliBookData($bookData);
        
        return $this->bookService->create(
            name: trim($bookData['name']),
            author: trim($bookData['author'])
        );
    }

    public function findBook(int $id): Book
    {
        return $this->bookService->find($id);
    }

    public function createMultipleBooks(array $booksData): array
    {
        // CLI might allow larger batches
        if (count($booksData) > 1000) {
            throw new \InvalidArgumentException('CLI allows maximum 1000 books at once');
        }
        
        $validatedBooks = [];
        foreach ($booksData as $index => $bookData) {
            $this->validateCliBookData($bookData, $index);
            $validatedBooks[] = [
                'name' => trim($bookData['name']),
                'author' => trim($bookData['author'])
            ];
        }
        
        return $this->bookService->createMultipleBooks($validatedBooks);
    }

    private function validateCliBookData(array $bookData, ?int $index = null): void
    {
        $prefix = $index !== null ? "Book at index $index: " : '';
        
        if (!isset($bookData['name']) || !is_string($bookData['name'])) {
            throw new \InvalidArgumentException($prefix . 'Book name is required and must be a string');
        }
        
        if (!isset($bookData['author']) || !is_string($bookData['author'])) {
            throw new \InvalidArgumentException($prefix . 'Author is required and must be a string');
        }
        
        // CLI might allow empty strings but trim them
        if (empty(trim($bookData['name']))) {
            throw new \InvalidArgumentException($prefix . 'Book name cannot be empty');
        }
        
        if (empty(trim($bookData['author']))) {
            throw new \InvalidArgumentException($prefix . 'Author cannot be empty');
        }
    }
}