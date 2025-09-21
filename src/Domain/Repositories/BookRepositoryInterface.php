<?php

declare(strict_types=1);

namespace Onion\Domain\Repositories;

use Onion\Domain\Entities\BookInterface;

interface BookRepositoryInterface
{
    public function create(BookInterface $book): BookInterface;
    public function findById(int $id): ?BookInterface;
    public function findOrFail(int $id): BookInterface;
    public function paginate(int $page = 1, int $size = 10): array;
    public function count(): int;
}
