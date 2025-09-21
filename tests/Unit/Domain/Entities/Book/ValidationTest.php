<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities\Book;

use Onion\Domain\Entities\BookInterface;
use Onion\Infrastructure\Entities\Book;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function test_constructor_throws_exception_when_provided_empty_title(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        $this->instance("", "", "");
    }

    public function test_constructor_throws_exception_when_provided_empty_author(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Author cannot be empty');

        $this->instance("Title", "", "");
    }

    public function test_constructor_throws_exception_when_provided_empty_description(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Description cannot be empty');

        $this->instance("Title", "Author", "");
    }

    public function test_constructor_throws_exception_when_provided_an_id_that_less_than_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id must be greater than 0');

        $this->instance("Title", "Author", "Description", id: -1);
    }

    public function test_constructor_throws_exception_when_provided_an_id_that_equals_to_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id must be greater than 0');

        $this->instance("Title", "Author", "Description", id: 0);
    }

    private function instance(
        string $title,
        string $author,
        string $description,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
        ?int $id = null
    ): BookInterface
    {
        return new Book($title, $author, $description, $createdAt, $updatedAt, $id);
    }
}
