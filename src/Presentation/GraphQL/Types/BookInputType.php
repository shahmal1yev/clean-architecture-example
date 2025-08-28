<?php

namespace Onion\Presentation\GraphQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class BookInputType extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'BookInput',
            'description' => 'Input for creating a book',
            'fields' => [
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The name of the book'
                ],
                'author' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The author of the book'
                ],
            ]
        ]);
    }
}