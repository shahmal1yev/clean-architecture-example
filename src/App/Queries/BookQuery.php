<?php

namespace Onion\App\Queries;

/**
 * CQRS Query Object
 * 
 * This demonstrates how to evolve your architecture to include
 * Command Query Responsibility Segregation (CQRS) patterns.
 * Queries are read-only operations that can be optimized
 * independently from Commands (write operations).
 */
readonly class BookQuery
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $author = null,
        public ?string $sortBy = 'id',
        public ?string $sortDirection = 'ASC',
        public int $limit = 50,
        public int $offset = 0
    ) {
        if ($this->limit > 100) {
            throw new \InvalidArgumentException('Limit cannot exceed 100');
        }
        
        if (!in_array($this->sortDirection, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('Sort direction must be ASC or DESC');
        }
        
        if (!in_array($this->sortBy, ['id', 'name', 'author', 'created_at'])) {
            throw new \InvalidArgumentException('Invalid sort field');
        }
    }
}