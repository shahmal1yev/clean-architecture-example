<?php

declare(strict_types=1);

namespace Onion\Domain\Entities;

interface BookInterface
{
    public ?int $id {
        get;
    }

    public string $title {
        get;
    }

    public string $author {
        get;
    }

    public string $description {
        get;
    }

    public ?\DateTimeImmutable $createdAt {
        get;
    }

    public ?\DateTimeImmutable $updatedAt {
        get;
    }

    public function validate();
}
