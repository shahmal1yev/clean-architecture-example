<?php

namespace Onion\Infrastructure\Repositories;

use Carbon\Carbon;
use Onion\Domain\Entities\Book;
use Onion\Domain\Repositories\BookRepositoryInterface;

readonly class BookRepository implements BookRepositoryInterface
{
    public function __construct(private AbstractRepository $repository)
    {
    }

    public function findById(int $id): Book
    {
        $data = $this->repository->findById($id);

        return new Book(
            $data['name'],
            $data['author'],
            $data['id'],
            Carbon::parse($data['created_at'])->toDateTimeImmutable(),
            Carbon::parse($data['updated_at'])->toDateTimeImmutable()
        );
    }

    public function save(Book $book): Book
    {
        $id = $this->repository->save([
            'name' => $book->getName(), 
            'author' => $book->getAuthor()
        ]);
        
        return $this->findById((int)$id);
    }
}
