<?php

namespace Onion\Presentation\Actions;

use Onion\App\Services\BookService;
use Onion\Domain\Exceptions\FindBookFailedException;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;
use Onion\Presentation\Http\JsonResponse;

readonly class FindBookAction
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $id = $this->extractBookId($request);
            
            $book = $this->bookService->find($id);
            
            return new JsonResponse([
                'success' => true,
                'data' => $book->toArray()
            ]);
            
        } catch (FindBookFailedException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Book not found',
                'message' => $e->getMessage()
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid book ID',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function extractBookId(Request $request): int
    {
        $id = $request->getPathParameter('id');
        
        if ($id === null) {
            throw new \InvalidArgumentException('Book ID is required');
        }
        
        if (!is_numeric($id) || (int)$id <= 0) {
            throw new \InvalidArgumentException('Book ID must be a positive integer');
        }
        
        return (int)$id;
    }
}