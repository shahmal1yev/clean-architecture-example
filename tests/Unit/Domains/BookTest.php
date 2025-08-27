<?php

namespace Tests\Unit\Domains;

use Carbon\Carbon;
use Onion\Domain\Entities\Book;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Builders\BookBuilder;
use Tests\TestCase;

#[CoversClass(Book::class)]
class BookTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_full_constructor(): void
    {
        $createdAt = Carbon::now()->toDateTimeImmutable();
        $updatedAt = Carbon::now()->addMinute()->toDateTimeImmutable();

        $book = new Book(1, 'Lord of Rings', 'J. R. Tolkien', $createdAt, $updatedAt);

        $this->assertSame(1, $book->getId());
        $this->assertSame('Lord of Rings', $book->getName());
        $this->assertSame('J. R. Tolkien', $book->getAuthor());
        $this->assertSame($createdAt, $book->getCreatedAt());
        $this->assertSame($updatedAt, $book->getUpdatedAt());
    }

    #[Test]
    public function it_can_be_created_without_id_and_timestamps(): void
    {
        $book = new Book(null, 'Test Book', 'Test Author');

        $this->assertNull($book->getId());
        $this->assertSame('Test Book', $book->getName());
        $this->assertSame('Test Author', $book->getAuthor());
        $this->assertNull($book->getCreatedAt());
        $this->assertNull($book->getUpdatedAt());
    }

    #[Test]
    public function it_implements_arrayable_interface(): void
    {
        $book = BookBuilder::create()
            ->withId(42)
            ->withName('Test Book')
            ->withAuthor('Test Author')
            ->withTimestamps()
            ->build();

        $array = $book->toArray();

        $this->assertIsArray($array);
        $this->assertSame(42, $array['id']);
        $this->assertSame('Test Book', $array['name']);
        $this->assertSame('Test Author', $array['author']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $array['createdAt']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $array['updatedAt']);
        $this->assertCount(5, $array);
    }

    #[Test]
    public function it_implements_json_serializable_interface(): void
    {
        $book = BookBuilder::create()
            ->withId(99)
            ->withName('Serializable Book')
            ->withAuthor('JSON Author')
            ->build();

        $jsonData = $book->jsonSerialize();

        $this->assertIsArray($jsonData);
        $this->assertSame(99, $jsonData['id']);
        $this->assertSame('Serializable Book', $jsonData['name']);
        $this->assertSame('JSON Author', $jsonData['author']);
    }

    #[Test]
    public function it_can_be_json_encoded(): void
    {
        $book = BookBuilder::create()
            ->withId(1)
            ->withName('JSON Book')
            ->withAuthor('Encode Author')
            ->build();

        $json = json_encode($book);
        $decoded = json_decode($json, true);

        $this->assertIsString($json);
        $this->assertSame(1, $decoded['id']);
        $this->assertSame('JSON Book', $decoded['name']);
        $this->assertSame('Encode Author', $decoded['author']);
    }

    #[Test]
    public function it_handles_empty_strings_correctly(): void
    {
        $book = new Book(null, '', '');

        $this->assertSame('', $book->getName());
        $this->assertSame('', $book->getAuthor());
    }

    #[Test]
    public function it_preserves_exact_datetime_instances(): void
    {
        $specificTime = Carbon::createFromFormat('Y-m-d H:i:s', '2024-01-01 12:00:00')->toDateTimeImmutable();
        
        $book = BookBuilder::create()
            ->withCreatedAt($specificTime)
            ->withUpdatedAt($specificTime)
            ->build();

        $this->assertSame($specificTime, $book->getCreatedAt());
        $this->assertSame($specificTime, $book->getUpdatedAt());
        $this->assertSame('2024-01-01 12:00:00', $book->getCreatedAt()->format('Y-m-d H:i:s'));
    }
}
