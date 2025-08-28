<?php

namespace Onion\Presentation\Actions;

use Onion\App\Queries\BookQuery;
use Onion\App\QueryHandlers\BookQueryHandler;
use Onion\Presentation\Http\JsonResponse;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;

/**
 * CQRS Query Action
 * 
 * This action demonstrates Command Query Responsibility Segregation.
 * It uses a Query Handler instead of an Application Service,
 * showing how read and write operations can be separated.
 */
readonly class SearchBooksAction
{
    public function __construct(
        private BookQueryHandler $queryHandler
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            // Extract query parameters from HTTP request
            $queryParams = $this->extractQueryParameters($request);
            
            // Create CQRS Query object
            $query = new BookQuery(
                id: $queryParams['id'],
                name: $queryParams['name'],
                author: $queryParams['author'],
                sortBy: $queryParams['sortBy'],
                sortDirection: $queryParams['sortDirection'],
                limit: $queryParams['limit'],
                offset: $queryParams['offset']
            );
            
            // Execute query through dedicated Query Handler
            $books = $this->queryHandler->handle($query);
            
            // Transform to HTTP response
            return new JsonResponse([
                'success' => true,
                'data' => array_map(fn($book) => $book->toArray(), $books),
                'count' => count($books),
                'query' => [
                    'limit' => $query->limit,
                    'offset' => $query->offset,
                    'sortBy' => $query->sortBy,
                    'sortDirection' => $query->sortDirection
                ]
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid query parameters',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Search failed',
                'message' => 'An error occurred while searching books'
            ], 500);
        }
    }

    private function extractQueryParameters(Request $request): array
    {
        $id = $request->getQueryParameter('id');
        $name = $request->getQueryParameter('name');
        $author = $request->getQueryParameter('author');
        $sortBy = $request->getQueryParameter('sortBy') ?? 'id';
        $sortDirection = $request->getQueryParameter('sortDirection') ?? 'ASC';
        $limit = (int)($request->getQueryParameter('limit') ?? 50);
        $offset = (int)($request->getQueryParameter('offset') ?? 0);

        return [
            'id' => $id ? (int)$id : null,
            'name' => $name ? trim($name) : null,
            'author' => $author ? trim($author) : null,
            'sortBy' => $sortBy,
            'sortDirection' => strtoupper($sortDirection),
            'limit' => min($limit, 100), // Cap at 100
            'offset' => max($offset, 0)  // No negative offsets
        ];
    }
}
