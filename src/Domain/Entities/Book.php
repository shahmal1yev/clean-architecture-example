<?php

namespace Onion\Domain\Entities;

use JsonSerializable;
use Onion\Domain\Entities\Interfaces\Arrayable;

readonly class Book implements JsonSerializable, Arrayable
{
    use Traits\Arrayable;
    use Traits\JsonSerializable;

    public function __construct(
        private string              $name,
        private string              $author,
        private ?int                $id = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
