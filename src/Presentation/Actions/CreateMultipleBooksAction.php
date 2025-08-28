<?php

namespace Onion\Presentation\Actions;

use Onion\App\Services\BookService;
use Onion\Domain\Exceptions\BookCreationFailedException;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;
use Onion\Presentation\Http\JsonResponse;

/**
 * Command Action for Bulk Book Creation
 * 
 * This action demonstrates Command pattern usage and
 * how to handle complex operations that involve multiple
 * domain operations within a single HTTP request.
 */
readonly class CreateMultipleBooksAction
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $booksData = $this->validateBulkInput($request);
            
            // Delegate complex operation to Application Service
            // The service handles transaction management internally
            $createdBooks = $this->bookService->createMultipleBooks($booksData);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Books created successfully',
                'data' => array_map(fn($book) => $book->toArray(), $createdBooks),
                'count' => count($createdBooks)
            ], 201);
            
        } catch (BookCreationFailedException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Bulk book creation failed',
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

    private function validateBulkInput(Request $request): array
    {
        $data = $request->getJsonData();
        
        if (!isset($data['books']) || !is_array($data['books'])) {
            throw new \InvalidArgumentException('Books array is required');
        }
        
        if (empty($data['books'])) {
            throw new \InvalidArgumentException('At least one book must be provided');
        }
        
        if (count($data['books']) > 50) { // Reasonable limit
            throw new \InvalidArgumentException('Maximum 50 books can be created at once');
        }
        
        $validatedBooks = [];
        
        foreach ($data['books'] as $index => $bookData) {
            if (!is_array($bookData)) {
                throw new \InvalidArgumentException("Book at index $index must be an object");
            }
            
            if (!isset($bookData['name']) || !is_string($bookData['name']) || empty(trim($bookData['name']))) {
                throw new \InvalidArgumentException("Book at index $index: name is required and must be a non-empty string");
            }
            
            if (!isset($bookData['author']) || !is_string($bookData['author']) || empty(trim($bookData['author']))) {
                throw new \InvalidArgumentException("Book at index $index: author is required and must be a non-empty string");
            }
            
            $validatedBooks[] = [
                'name' => trim($bookData['name']),
                'author' => trim($bookData['author'])
            ];
        }
        
        return $validatedBooks;
    }
}