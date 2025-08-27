<?php

namespace Tests\Unit\Domains\Traits;

use Onion\Domain\Entities\Interfaces\Arrayable as ArrayableInterface;
use Onion\Domain\Entities\Traits\Arrayable;
use Onion\Domain\Entities\Traits\JsonSerializable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

#[CoversClass(JsonSerializable::class)]
class JsonSerializableTraitTest extends TestCase
{
    #[Test]
    public function it_serializes_arrayable_object_to_json(): void
    {
        $testObject = new class implements ArrayableInterface {
            use Arrayable, JsonSerializable;
            
            private string $name = 'JSON Test';
            private int $value = 42;
            private bool $active = true;
        };

        $result = $testObject->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertSame('JSON Test', $result['name']);
        $this->assertSame(42, $result['value']);
        $this->assertTrue($result['active']);
        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_can_be_used_with_json_encode(): void
    {
        $testObject = new class('Encodable Object', ['php', 'test']) implements ArrayableInterface, \JsonSerializable {
            use Arrayable, JsonSerializable;
            
            public function __construct(
                private readonly string $title,
                private readonly array $tags
            ) {}
        };

        $json = json_encode($testObject);
        $decoded = json_decode($json, true);

        $this->assertIsString($json);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('title', $decoded);
        $this->assertArrayHasKey('tags', $decoded);
        $this->assertSame('Encodable Object', $decoded['title']);
        $this->assertSame(['php', 'test'], $decoded['tags']);
    }

    #[Test]
    public function it_throws_exception_when_not_arrayable(): void
    {
        $nonArrayableObject = new class {
            use JsonSerializable; // Using trait without implementing Arrayable
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('jsonSerialize is not implemented');

        $nonArrayableObject->jsonSerialize();
    }

    #[Test]
    public function it_handles_empty_arrayable_object(): void
    {
        $emptyObject = new class implements ArrayableInterface {
            use Arrayable, JsonSerializable;
            // No properties
        };

        $result = $emptyObject->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_preserves_nested_data_structures(): void
    {
        $complexObject = new class implements ArrayableInterface {
            use Arrayable, JsonSerializable;
            
            private array $data = [
                'nested' => ['key' => 'value'],
                'numbers' => [1, 2, 3]
            ];
            private ?\DateTime $timestamp = null;
            
            public function __construct()
            {
                $this->timestamp = new \DateTime('2024-01-01T12:00:00Z');
            }
        };

        $result = $complexObject->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertSame(['key' => 'value'], $result['data']['nested']);
        $this->assertSame([1, 2, 3], $result['data']['numbers']);
        $this->assertInstanceOf(\DateTime::class, $result['timestamp']);
    }

    #[Test]
    public function it_works_with_json_encode_flags(): void
    {
        $testObject = new class('café', '<script>alert("test")</script>') implements ArrayableInterface, \JsonSerializable {
            use Arrayable, JsonSerializable;
            
            public function __construct(
                private string $unicode,
                private string $html
            ) {}
        };

        $prettyJson = json_encode($testObject, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $decoded = json_decode($prettyJson, true);

        $this->assertIsString($prettyJson);
        $this->assertStringContainsString('café', $prettyJson); // Unicode preserved
        $this->assertSame('café', $decoded['unicode']);
        $this->assertSame('<script>alert("test")</script>', $decoded['html']);
    }
}
