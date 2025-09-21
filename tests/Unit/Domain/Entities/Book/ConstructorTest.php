<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities\Book;

use Onion\Domain\Entities\BookInterface;
use Onion\Infrastructure\Entities\Book;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConstructorTest extends TestCase
{
    public function test_constructor_can_create_an_instance(): void
    {
        $book = $this->instance('BookInterface 1', 'Author 1', 'Desc of BookInterface 1');

        $this->assertInstanceOf(BookInterface::class, $book);
    }

    #[DataProvider('optionalValuesProvider')]
    public function test_constructor_can_create_an_instance_even_optional_values_are_provided(array $optionalValues): void
    {
        $book = $this->instance('Book 2',
            'Author 2',
            'Desc of BookInterface 2',
            ...$optionalValues
        );

        $this->assertInstanceOf(BookInterface::class, $book);
    }

    public static function optionalValuesProvider(): \Generator
    {
        yield [['createdAt' => new \DateTimeImmutable()]];
        yield [['createdAt' => new \DateTimeImmutable(), 'updatedAt' => new \DateTimeImmutable()]];
        yield [['createdAt' => new \DateTimeImmutable(), 'updatedAt' => new \DateTimeImmutable(), 'id' => 1]];
        yield [['updatedAt' => new \DateTimeImmutable(), 'id' => 1]];
        yield [['id' => 2]];
        yield [['updatedAt' => new \DateTimeImmutable()]];
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
