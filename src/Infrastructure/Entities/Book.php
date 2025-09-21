<?php

namespace Onion\Infrastructure\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Onion\Domain\Entities\BookInterface;

#[Entity]
#[Table(name: "books")]
class Book implements BookInterface
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    public ?int $id = null;

    #[Column(type: 'string', length: 255)]
    public string $title;

    #[Column(type: 'string', length: 255)]
    public string $author;

    #[Column(type: 'string', length: 255)]
    public string $description;

    #[Column(name: 'created_at', type: 'datetime_immutable')]
    public ?\DateTimeImmutable $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime_immutable')]
    public ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        string $title,
        string $author,
        string $description,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
        ?int $id = null
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->validate();
    }

    public function validate(): void
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        if (empty($this->author)) {
            throw new \InvalidArgumentException('Author cannot be empty');
        }

        if (empty($this->description)) {
            throw new \InvalidArgumentException('Description cannot be empty');
        }

        if (is_int($this->id) && 0 >= $this->id) {
            throw new \InvalidArgumentException('Id must be greater than 0');
        }
    }
}
