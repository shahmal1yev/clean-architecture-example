<?php

namespace Onion\Presentation\Actions;

use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Presentation\Http\JsonResponse;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;
use Onion\Presentation\Ports\BookManagementPort;

/**
 * Hexagonal Architecture + ADR Action (Version 2)
 * 
 * This demonstrates the evolved approach using Ports & Adapters
 * pattern. The Action depends on a Port (interface) rather than
 * concrete implementations, making it more testable and flexible.
 */
readonly class CreateBookActionV2
{
    public function __construct(
        private BookManagementPort $bookManagement
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            // Extract HTTP-specific concerns (JSON parsing, etc.)
            $bookData = $request->getJsonData();
            
            // Delegate to Port (implemented by Adapter)
            // Port handles all validation and business logic delegation
            $book = $this->bookManagement->createBook($bookData);
            
            // Transform domain entity to HTTP response format
            return new JsonResponse([
                'success' => true,
                'data' => $book->toArray(),
                'id' => $book->getId()
            ], 201);
            
        } catch (BookCreationFailedException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Book creation failed',
                'message' => $e->getMessage()
            ], 400);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid input',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
