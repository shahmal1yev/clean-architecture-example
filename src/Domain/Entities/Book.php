<?php

namespace Onion\Domain\Entities;

use JsonSerializable;
use Onion\Domain\Entities\Interfaces\Arrayable;

class Book implements JsonSerializable, Arrayable
{
    use Traits\Arrayable;
    use Traits\JsonSerializable;

    public function __construct(
        private readonly ?int                $id = null,
        private readonly string              $name,
        private readonly string              $author,
        private readonly ?\DateTimeImmutable $createdAt = null,
        private readonly ?\DateTimeImmutable $updatedAt = null,
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
