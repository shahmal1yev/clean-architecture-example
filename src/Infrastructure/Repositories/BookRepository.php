<?php

declare(strict_types=1);

namespace Onion\Infrastructure\Repositories;

use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Onion\Domain\Entities\BookInterface;
use Onion\Domain\Exceptions\Book\BookCreationFailedException;
use Onion\Domain\Exceptions\Book\BookNotFoundException;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\Entities\Book;

final readonly class BookRepository implements BookRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws BookCreationFailedException
     */
    public function create(BookInterface $book): BookInterface
    {
        $book->createdAt = new DateTimeImmutable();
        $book->updatedAt = new DateTimeImmutable();

        try {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            throw new BookCreationFailedException("Book creation failed: " . $exception->getMessage());
        }

        return $book;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function findById(int $id): ?BookInterface
    {
        return $this->entityManager->find(Book::class, $id);
    }

    /**
     * @throws OptimisticLockException
     * @throws BookNotFoundException
     * @throws ORMException
     */
    public function findOrFail(int $id): BookInterface
    {
        $book = $this->entityManager->find(Book::class, $id);

        if (null === $book) {
            throw new BookNotFoundException("Book not found: $id");
        }

        return $book;
    }

    public function paginate(int $page = 1, int $size = 10): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('book')
            ->from(Book::class, 'book')
            ->orderBy('book.id', 'ASC')
            ->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size)
            ->getQuery()
            ->getResult();
    }

    public function count(): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(books.id)')
            ->from(Book::class, 'books')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
