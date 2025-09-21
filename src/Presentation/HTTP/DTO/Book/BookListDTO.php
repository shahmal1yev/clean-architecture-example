<?php

namespace Onion\Presentation\HTTP\DTO\Book;

use Onion\Domain\Entities\BookInterface;
use Onion\Presentation\HTTP\Interface\DTOInterface;

readonly class BookListDTO implements DTOInterface
{
    private array $books;

    public function __construct(
        array       $books,
        private int $page = 1,
        private int $size = 10,
        private int $total = 0,
    )
    {
        $this->books = array_map(fn(BookInterface $book) => new ReadBookDTO($book)->jsonSerialize()['data'], $books);
    }

    public function jsonSerialize(): array
    {
        $lastPage = (int) ceil($this->total / $this->size);

        return [
            'data' => $this->books,
            'meta' => [
                'per_page' => $this->size,
                'page' => $this->page,
                'total' => $this->total,
            ],
            'links' => [
                'first_page_url' => \env('APP_URL') . '/books?page=1&size=' . $this->size,
                'prev_page_url' => \env('APP_URL') . '/books?page=' . max(1, $this->page - 1) . '&size=' . $this->size,
                'current_page_url' => \env('APP_URL') . '/books?page=' . $this->page . '&size=' . $this->size,
                'next_page_url' => \env('APP_URL') . '/books?page=' . min($lastPage, $this->page + 1) . '&size=' . $this->size,
                'last_page_url' => \env('APP_URL') . '/books?page=' . $lastPage . '&size=' . $this->size,
            ]
        ];
    }
}
