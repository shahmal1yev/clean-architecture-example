<?php

namespace Onion\App\UseCases;

use Onion\App\Interfaces\UseCases\ReadBookInterface;
use Onion\Domain\Entities\BookInterface;
use Onion\Domain\Repositories\BookRepositoryInterface;

final readonly class ReadBook implements ReadBookInterface
{
    public function __construct(private BookRepositoryInterface $repository)
    {
    }

    public function __invoke(int $id): BookInterface
    {
        if (0 >= $id) {
            throw new \InvalidArgumentException("ID must be greater than 0");
        }

        return $this->repository->findOrFail($id);
    }

    public function execute(int $id): BookInterface
    {
        return $this->__invoke($id);
    }
}
