<?php

namespace Onion\Presentation\Ports;

use Onion\Domain\Entities\Book;

/**
 * Hexagonal Architecture Port
 * 
 * This interface defines how the outside world can interact
 * with your book management capabilities. It's implemented
 * by adapters (like HTTP actions, CLI commands, etc.)
 */
interface BookManagementPort
{
    /**
     * Create a new book
     * 
     * @param array{name: string, author: string} $bookData
     * @return Book
     * @throws \InvalidArgumentException When input is invalid
     * @throws \Onion\Domain\Exceptions\BookCreationFailedException
     */
    public function createBook(array $bookData): Book;

    /**
     * Find book by ID
     * 
     * @throws \Onion\Domain\Exceptions\FindBookFailedException
     */
    public function findBook(int $id): Book;

    /**
     * Create multiple books atomically
     * 
     * @param array<array{name: string, author: string}> $booksData
     * @return array<Book>
     * @throws \InvalidArgumentException When input is invalid
     * @throws \Onion\Domain\Exceptions\BookCreationFailedException
     */
    public function createMultipleBooks(array $booksData): array;
}