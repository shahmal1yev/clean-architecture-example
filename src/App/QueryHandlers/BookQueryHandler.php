<?php

namespace Onion\App\QueryHandlers;

use Onion\App\Queries\BookQuery;
use Onion\Domain\Entities\Book;
use Onion\Domain\Repositories\BookRepositoryInterface;

/**
 * CQRS Query Handler
 * 
 * Query handlers are separate from Command handlers (like BookService)
 * and can be optimized for read operations. They can use different
 * data stores, caching strategies, or even separate read models.
 */
readonly class BookQueryHandler
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {
    }

    /**
     * Handle book search queries
     * 
     * @return array<Book>
     */
    public function handle(BookQuery $query): array
    {
        // In a more advanced implementation, this could:
        // 1. Use a separate read-optimized database
        // 2. Implement caching strategies
        // 3. Use search engines like Elasticsearch
        // 4. Return DTOs instead of domain entities
        
        if ($query->id !== null) {
            return [$this->repository->findById($query->id)];
        }
        
        // For now, delegate to repository
        // In real implementation, you'd add search capabilities
        return $this->searchBooks($query);
    }

    /**
     * Simplified search implementation
     * 
     * In a real application, this would be implemented in the repository
     * with proper SQL queries, full-text search, etc.
     */
    private function searchBooks(BookQuery $query): array
    {
        // This is a simplified implementation
        // Real implementation would use repository methods with proper SQL
        
        // For demonstration, we'll just return a single book if found
        if ($query->name !== null || $query->author !== null) {
            // This would typically be a repository method like:
            // return $this->repository->search($query);
            
            // For now, just return empty array as we don't have search implemented
            return [];
        }
        
        // Default: return all (with pagination in real implementation)
        return [];
    }
}