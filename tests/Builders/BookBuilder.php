<?php

namespace Tests\Builders;

use Carbon\Carbon;
use Onion\Domain\Entities\Book;

final class BookBuilder
{
    private ?int $id = null;
    private string $name = 'Default Book Title';
    private string $author = 'Default Author';
    private ?\DateTimeImmutable $createdAt = null;
    private ?\DateTimeImmutable $updatedAt = null;

    public static function create(): self
    {
        return new self();
    }

    public function withId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function withTimestamps(): self
    {
        $now = Carbon::now()->toDateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        return $this;
    }

    public function persistent(): self
    {
        return $this->withId(1)->withTimestamps();
    }

    public function build(): Book
    {
        return new Book(
            $this->id,
            $this->name,
            $this->author,
            $this->createdAt,
            $this->updatedAt
        );
    }
}