<?php

namespace Tests\Feature\Presentation;

use Onion\Presentation\Actions\CreateBookAction;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\JsonResponse;
use Tests\TestCase;

/**
 * Integration test demonstrating full ADR flow
 * 
 * This test shows how the presentation layer integrates
 * with the application services while maintaining proper
 * architectural boundaries.
 */
class CreateBookActionTest extends TestCase
{
    private CreateBookAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = $this->container->get(CreateBookAction::class);
    }

    public function test_create_book_success(): void
    {
        // Arrange: Create a mock HTTP request
        $requestData = [
            'name' => 'Clean Architecture',
            'author' => 'Robert C. Martin'
        ];
        
        $request = new Request(
            method: 'POST',
            uri: '/books',
            headers: ['content-type' => 'application/json'],
            body: json_encode($requestData)
        );

        // Act: Invoke the action
        $response = ($this->action)($request);

        // Assert: Verify response structure and data
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = $response->getData();
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Clean Architecture', $responseData['data']['name']);
        $this->assertEquals('Robert C. Martin', $responseData['data']['author']);
        $this->assertIsInt($responseData['id']);
    }

    public function test_create_book_validation_error(): void
    {
        // Arrange: Invalid request data
        $requestData = [
            'name' => '', // Empty name should fail validation
            'author' => 'Robert C. Martin'
        ];
        
        $request = new Request(
            method: 'POST',
            uri: '/books',
            headers: ['content-type' => 'application/json'],
            body: json_encode($requestData)
        );

        // Act: Invoke the action
        $response = ($this->action)($request);

        // Assert: Verify error response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseData = $response->getData();
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Invalid input', $responseData['error']);
        $this->assertStringContains('name is required', $responseData['message']);
    }

    public function test_create_book_invalid_json(): void
    {
        // Arrange: Invalid JSON
        $request = new Request(
            method: 'POST',
            uri: '/books',
            headers: ['content-type' => 'application/json'],
            body: 'invalid json'
        );

        // Act: Invoke the action
        $response = ($this->action)($request);

        // Assert: Verify error response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseData = $response->getData();
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Invalid input', $responseData['error']);
    }
}