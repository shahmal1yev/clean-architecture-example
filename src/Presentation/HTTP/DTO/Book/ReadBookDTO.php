<?php

declare(strict_types=1);

namespace Onion\Presentation\HTTP\DTO\Book;

use Onion\Domain\Entities\BookInterface;
use Onion\Presentation\HTTP\Interface\DTOInterface;

final readonly class ReadBookDTO implements DTOInterface
{
    public function __construct(private BookInterface $book)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'data' => [

                'id' => $this->book->id,
                'title' => $this->book->title,
                'author' => $this->book->author,
                'description' => $this->book->description,
                'created_at' => $this->book->createdAt->format(DATE_ATOM),
                'updated_at' => $this->book->updatedAt->format(DATE_ATOM),
            ]
        ];
    }
}
