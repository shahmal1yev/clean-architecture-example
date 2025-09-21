<?php

namespace Onion\App\UseCases;

use InvalidArgumentException;
use Onion\App\Interfaces\UseCases\CreateBookInterface;
use Onion\Domain\Entities\BookInterface;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Entities\Book;
 

final readonly class CreateBook implements CreateBookInterface
{
    public function __construct(private BookRepositoryInterface $repository)
    {
    }

    /** @throws InvalidArgumentException */
    public function __invoke(array $data): BookInterface
    {
        $book = new Book($data['title'] ?? '', $data['author'] ?? '', $data['description'] ?? '');
        return $this->repository->create($book);
    }

    public function execute(array $data): BookInterface
    {
        return $this->__invoke($data);
    }
}
