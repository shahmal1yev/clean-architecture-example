<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Onion\Infrastructure\DependencyInjection\ContainerFactory;
use Onion\Presentation\GraphQL\GraphQLController;
use Onion\Presentation\Http\Request;

// Set up DI Container
$container = ContainerFactory::create(__DIR__ . '/../config');

// Get GraphQL Controller
$controller = $container->get(GraphQLController::class);

echo "=== GraphQL Implementation Example ===\n\n";

// Example 1: Create a book mutation
echo "1. Creating a book via GraphQL:\n";
$createBookQuery = [
    'query' => 'mutation CreateBook($input: BookInput!) { 
        createBook(input: $input) { 
            id 
            name 
            author 
            createdAt 
        } 
    }',
    'variables' => [
        'input' => [
            'name' => 'Clean Architecture',
            'author' => 'Robert C. Martin',
        ]
    ]
];

$request = new Request('POST', '/graphql', [], json_encode($createBookQuery));
$response = $controller->handle($request);
$data = json_decode($response->getContent(), true);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Extract book ID from response for query example
$bookId = $data['data']['createBook']['id'] ?? null;

// Example 2: Query a book
if ($bookId) {
    echo "2. Querying the created book:\n";
    $queryBookQuery = [
        'query' => 'query GetBook($id: ID!) { 
            book(id: $id) { 
                name 
                author 
            } 
        }',
        'variables' => ['id' => (string)$bookId]
    ];

    $request = new Request('POST', '/graphql', [], json_encode($queryBookQuery));
    $response = $controller->handle($request);
    $data = json_decode($response->getContent(), true);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
}

// Example 3: Create multiple books
echo "3. Creating multiple books:\n";
$createMultipleBooksQuery = [
    'query' => 'mutation CreateBooks($input: [BookInput!]!) { 
        createMultipleBooks(input: $input) { 
            id 
            name 
            author 
        } 
    }',
    'variables' => [
        'input' => [
            ['name' => 'Domain-Driven Design', 'author' => 'Eric Evans'],
            ['name' => 'Refactoring', 'author' => 'Martin Fowler']
        ]
    ]
];

$request = new Request('POST', '/graphql', [], json_encode($createMultipleBooksQuery));
$response = $controller->handle($request);
$data = json_decode($response->getContent(), true);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Example 4: Error handling - invalid query
echo "4. Error handling example (missing query):\n";
$invalidQuery = ['variables' => ['test' => 'value']];

$request = new Request('POST', '/graphql', [], json_encode($invalidQuery));
$response = $controller->handle($request);
$data = json_decode($response->getContent(), true);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

echo "=== GraphQL Schema Introspection Query ===\n";
$introspectionQuery = [
    'query' => '
        query IntrospectionQuery {
            __schema {
                types {
                    name
                    kind
                    description
                }
            }
        }
    '
];

$request = new Request('POST', '/graphql', [], json_encode($introspectionQuery));
$response = $controller->handle($request);
$data = json_decode($response->getContent(), true);

echo "Available types in schema:\n";
foreach ($data['data']['__schema']['types'] as $type) {
    if (!str_starts_with($type['name'], '__')) {
        echo "- {$type['name']} ({$type['kind']}): {$type['description']}\n";
    }
}

echo "\n=== Implementation Complete! ===\n";
echo "GraphQL has been successfully integrated into your Onion Architecture.\n";
echo "Key architectural benefits:\n";
echo "- Clean separation: GraphQL is purely presentation layer\n";
echo "- Business logic unchanged: BookService remains GraphQL-agnostic\n";
echo "- Port/Adapter pattern: GraphBookManagementAdapter translates GraphQL to domain\n";
echo "- Testable: Full test coverage with unit and feature tests\n";
echo "- Dependency Injection: Properly configured in Symfony DI container\n";
