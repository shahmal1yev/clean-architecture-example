<?php

namespace Onion\Presentation\GraphQL\Resolvers;

use Onion\Domain\Entities\Book;
use Onion\Presentation\Ports\BookManagementPort;

readonly class BookResolver
{
    public function __construct(
        private BookManagementPort $bookManagement
    ) {
    }

    public function createBook(array $args): Book
    {
        return $this->bookManagement->createBook($args['input']);
    }

    public function findBook(array $args): Book
    {
        return $this->bookManagement->findBook($args['id']);
    }

    public function createMultipleBooks(array $args): array
    {
        return $this->bookManagement->createMultipleBooks($args['input']);
    }
}