<?php

namespace Onion\Presentation\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Onion\Presentation\GraphQL\Resolvers\BookResolver;
use Onion\Presentation\GraphQL\Types\BookInputType;
use Onion\Presentation\GraphQL\Types\BookType;

readonly class SchemaBuilder
{
    public function __construct(
        private BookResolver $bookResolver,
        private BookType $bookType,
        private BookInputType $bookInputType
    ) {
    }

    public function build(): Schema
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'book' => [
                    'type' => $this->bookType,
                    'description' => 'Find a book by ID',
                    'args' => [
                        'id' => [
                            'type' => Type::nonNull(Type::id()),
                            'description' => 'The ID of the book to find'
                        ]
                    ],
                    'resolve' => fn($root, $args) => $this->bookResolver->findBook($args)
                ],
            ]
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createBook' => [
                    'type' => $this->bookType,
                    'description' => 'Create a new book',
                    'args' => [
                        'input' => [
                            'type' => Type::nonNull($this->bookInputType),
                            'description' => 'The book data to create'
                        ]
                    ],
                    'resolve' => fn($root, $args) => $this->bookResolver->createBook($args)
                ],
                'createMultipleBooks' => [
                    'type' => Type::listOf($this->bookType),
                    'description' => 'Create multiple books',
                    'args' => [
                        'input' => [
                            'type' => Type::nonNull(Type::listOf(Type::nonNull($this->bookInputType))),
                            'description' => 'Array of book data to create'
                        ]
                    ],
                    'resolve' => fn($root, $args) => $this->bookResolver->createMultipleBooks($args)
                ],
            ]
        ]);

        return new Schema([
            'query' => $queryType,
            'mutation' => $mutationType,
        ]);
    }
}