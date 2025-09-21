<?php

namespace Onion\App\Interfaces\UseCases;

use Onion\Domain\Entities\BookInterface;

interface ListBooksInterface
{
    public function __invoke(int $page = 1, int $size = 10): array;
    public function execute(int $page = 1, int $size = 10): array;
}
