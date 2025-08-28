<?php

namespace Onion\Infrastructure\Factory\Repositories;

use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Repositories\BookRepository;

readonly class BookRepositoryFactory
{
    public function __construct(
        private AbstractRepositoryFactory $abstractRepositoryFactory,
    )
    {
    }

    public function create(): BookRepositoryInterface
    {
        return new BookRepository($this->abstractRepositoryFactory->create('books'));
    }
}
