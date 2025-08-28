<?php

namespace Onion\Infrastructure\Repositories;

use Carbon\Carbon;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Domain\Entities\Book;
use PDO;
use PDOException;

readonly class BookRepository extends Repository implements BookRepositoryInterface
{
    public function tableName(): string
    {
        return 'books';
    }

    public function findById(int $id): Book
    {
        $data = parent::abstractFindById($id);

        return new Book(
            $data['id'],
            $data['name'],
            $data['author'],
            Carbon::parse($data['created_at'])->toDateTimeImmutable(),
            Carbon::parse($data['updated_at'])->toDateTimeImmutable()
        );
    }

    public function save(Book $book): Book
    {
        return $this->findById(
            parent::abstractSave(['name' => $book->getName(), 'author' => $book->getAuthor()])
        );
    }
}
