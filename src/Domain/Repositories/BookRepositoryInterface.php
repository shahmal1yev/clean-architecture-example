<?php

namespace Onion\Domain\Repositories;

use Onion\Domain\Entities\Book;

interface BookRepositoryInterface
{
    public function findById(int $id): Book;
    public function save(Book $book): Book;
}
