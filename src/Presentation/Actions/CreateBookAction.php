<?php

namespace Onion\Presentation\Actions;

use Onion\App\Services\BookService;
use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Presentation\Http\JsonResponse;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;

readonly class CreateBookAction
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            // Input validation and transformation
            $data = $this->validateInput($request);
            
            // Delegate to Application Service
            $book = $this->bookService->create(
                name: $data['name'],
                author: $data['author']
            );
            
            // Transform domain entity to HTTP response
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

    private function validateInput(Request $request): array
    {
        $data = $request->getJsonData();
        
        if (!isset($data['name']) || !is_string($data['name']) || empty(trim($data['name']))) {
            throw new \InvalidArgumentException('Book name is required and must be a non-empty string');
        }
        
        if (!isset($data['author']) || !is_string($data['author']) || empty(trim($data['author']))) {
            throw new \InvalidArgumentException('Author is required and must be a non-empty string');
        }
        
        return [
            'name' => trim($data['name']),
            'author' => trim($data['author'])
        ];
    }
}
