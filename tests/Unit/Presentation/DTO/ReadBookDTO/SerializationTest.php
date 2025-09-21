<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\DTO\ReadBookDTO;

use Onion\Domain\Entities\BookInterface;
use Onion\Presentation\HTTP\DTO\Book\ReadBookDTO;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SerializationTest extends TestCase
{
    private \DateTimeImmutable $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = new \DateTimeImmutable();
    }

    #[DataProvider('transformerProvider')]
    public function test_it_serializes_book_as_expected(\Closure $transformer): void
    {
        $dto = new ReadBookDTO($this->mockBook());
        $actual = $transformer($dto);

        foreach ($this->expectedDataStructure() as $fieldName => $dataType) {
            $this->assertSame($dataType, gettype($actual['data'][$fieldName]));
        }
    }

    public static function transformerProvider(): \Generator
    {
        yield [fn(ReadBookDTO $dto): array => json_decode(json_encode($dto), true)];
        yield [fn(ReadBookDTO $dto): array => $dto->jsonSerialize()];
    }

    private function expectedDataStructure(): array
    {
        return [
            'id' => 'integer',
            'title' => 'string',
            'author' => 'string',
            'description' => 'string',
            'created_at' => 'string',
            'updated_at' => 'string',
        ];
    }

    private function mockBook(): BookInterface
    {
        $bookMock = new FakeBook();

        $bookMock->id = 12;
        $bookMock->title = 'title';
        $bookMock->author = 'author';
        $bookMock->description = 'description';
        $bookMock->createdAt = $this->now;
        $bookMock->updatedAt = $this->now;

        return $bookMock;
    }
}


final class FakeBook implements BookInterface
{
    public int|null $id;
    public string $title;
    public string $author;
    public string $description;
    public \DateTimeImmutable|null $createdAt;
    public \DateTimeImmutable|null $updatedAt;

    public function validate()
    {
        // TODO: Implement validate() method.
    }
}
