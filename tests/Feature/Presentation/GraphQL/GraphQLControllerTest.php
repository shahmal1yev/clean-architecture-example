<?php

namespace Tests\Feature\Presentation\GraphQL;

use Onion\Domain\Entities\Book;
use Onion\Presentation\GraphQL\GraphQLController;
use Onion\Presentation\Http\Request;
use Tests\TestCase;

class GraphQLControllerTest extends TestCase
{
    private GraphQLController $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->controller = self::$container->get(GraphQLController::class);
    }

    public function test_query_book_success(): void
    {
        // First create a book to query
        $bookService = self::$container->get(\Onion\App\Services\BookService::class);
        $book = $bookService->create('Test Book', 'Test Author');
        
        $query = [
            'query' => 'query GetBook($id: ID!) { book(id: $id) { id name author } }',
            'variables' => ['id' => (string)$book->getId()]
        ];

        $request = new Request('POST', '/graphql', [], json_encode($query));
        $response = $this->controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('book', $responseData['data']);
        $this->assertEquals($book->getId(), $responseData['data']['book']['id']);
        $this->assertEquals('Test Book', $responseData['data']['book']['name']);
        $this->assertEquals('Test Author', $responseData['data']['book']['author']);
    }

    public function test_mutation_create_book_success(): void
    {
        $query = [
            'query' => 'mutation CreateBook($input: BookInput!) { createBook(input: $input) { id name author } }',
            'variables' => [
                'input' => [
                    'name' => 'GraphQL Book',
                    'author' => 'GraphQL Author'
                ]
            ]
        ];

        $request = new Request('POST', '/graphql', [], json_encode($query));
        $response = $this->controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('createBook', $responseData['data']);
        $this->assertNotNull($responseData['data']['createBook']['id']);
        $this->assertEquals('GraphQL Book', $responseData['data']['createBook']['name']);
        $this->assertEquals('GraphQL Author', $responseData['data']['createBook']['author']);
    }

    public function test_mutation_create_multiple_books_success(): void
    {
        $query = [
            'query' => 'mutation CreateBooks($input: [BookInput!]!) { createMultipleBooks(input: $input) { id name author } }',
            'variables' => [
                'input' => [
                    ['name' => 'Book 1', 'author' => 'Author 1'],
                    ['name' => 'Book 2', 'author' => 'Author 2']
                ]
            ]
        ];

        $request = new Request('POST', '/graphql', [], json_encode($query));
        $response = $this->controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('createMultipleBooks', $responseData['data']);
        $this->assertCount(2, $responseData['data']['createMultipleBooks']);
    }

    public function test_invalid_json_request(): void
    {
        $request = new Request('POST', '/graphql', [], 'invalid json');
        $response = $this->controller->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Invalid JSON', $responseData['error']);
    }

    public function test_missing_query(): void
    {
        $request = new Request('POST', '/graphql', [], '{}');
        $response = $this->controller->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Missing query', $responseData['error']);
    }
}