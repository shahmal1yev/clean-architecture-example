<?php

namespace Onion\App\UseCases;

use Onion\App\Interfaces\UseCases\ListBooksInterface;
use Onion\Domain\Repositories\BookRepositoryInterface;

final readonly class ListBooks implements ListBooksInterface
{
    public function __construct(
        private BookRepositoryInterface $repository,
    )
    {
    }

    /** @inheritDoc */
    public function __invoke(int $page = 1, int $size = 10): array
    {
        if ($page <= 0) {
            throw new \InvalidArgumentException('Page must be greater than 0');
        }

        if ($size <= 0) {
            throw new \InvalidArgumentException('Size must be greater than 0');
        }

        return [
            'items' => $this->repository->paginate($page, $size),
            'page' => $page,
            'per_page' => $size,
            'total' => $count = $this->repository->count(),
            'page_count' => ceil($count / $size),
        ];
    }

    public function execute(int $page = 1, int $size = 10): array
    {
        return $this($page, $size);
    }
}
