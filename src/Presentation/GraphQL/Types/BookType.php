<?php

namespace Onion\Presentation\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Onion\Domain\Entities\Book;

class BookType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Book',
            'description' => 'A book entity',
            'fields' => [
                'id' => [
                    'type' => Type::id(),
                    'description' => 'The unique identifier of the book',
                    'resolve' => fn(Book $book) => $book->getId()
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The name of the book',
                    'resolve' => fn(Book $book) => $book->getName()
                ],
                'author' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The author of the book',
                    'resolve' => fn(Book $book) => $book->getAuthor()
                ],
                'createdAt' => [
                    'type' => Type::string(),
                    'description' => 'When the book was created',
                    'resolve' => fn(Book $book) => $book->getCreatedAt()?->format('c')
                ],
                'updatedAt' => [
                    'type' => Type::string(),
                    'description' => 'When the book was last updated',
                    'resolve' => fn(Book $book) => $book->getUpdatedAt()?->format('c')
                ],
            ]
        ]);
    }
}