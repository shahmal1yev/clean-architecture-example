<?php

namespace Onion\Infrastructure\Repositories;

use Carbon\Carbon;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Domain\Entities\Book;
use PDO;
use PDOException;

readonly class BookRepository extends Repository implements BookRepositoryInterface
{
    public function findById(int $id): Book
    {
        $data = $this->pdo()->query("SELECT * FROM books WHERE id = $id")->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException("Book not found: $id");
        }

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
        try {
            $this->pdo()->beginTransaction();

            $bookData = ['name' => $book->getName(), 'author' => $book->getAuthor()];

            $stmt = $this->pdo()->prepare("INSERT INTO public.books(name, author) VALUES (:name, :author)");

            $stmt->bindParam(':name', $bookData['name']);
            $stmt->bindParam(':author', $bookData['author']);

            $stmt->execute();

            $this->pdo()->commit();

            $id = $this->pdo()->lastInsertId();

            return $this->findById($id);
        } catch (PDOException $e) {
            $this->pdo()->rollBack();
            throw new PDOException(
                "An error occurred while saving new book: {$e->getMessage()}",
                (int) $e->getCode(),
                $e
            );
        }
    }
}
