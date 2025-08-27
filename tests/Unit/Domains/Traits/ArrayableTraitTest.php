<?php

namespace Tests\Unit\Domains\Traits;

use Onion\Domain\Entities\Traits\Arrayable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(Arrayable::class)]
class ArrayableTraitTest extends TestCase
{
    #[Test]
    public function it_converts_object_properties_to_array(): void
    {
        $testObject = new class {
            use Arrayable;
            
            private string $name = 'Test Name';
            private int $age = 25;
            private ?bool $active = true;
            private ?\DateTime $date = null;
            
            public function __construct()
            {
                $this->date = new \DateTime('2024-01-01');
            }
        };

        $result = $testObject->toArray();

        $this->assertIsArray($result);
        $this->assertSame('Test Name', $result['name']);
        $this->assertSame(25, $result['age']);
        $this->assertTrue($result['active']);
        $this->assertInstanceOf(\DateTime::class, $result['date']);
        $this->assertCount(4, $result);
    }

    #[Test]
    public function it_handles_empty_object(): void
    {
        $emptyObject = new class {
            use Arrayable;
        };

        $result = $emptyObject->toArray();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_handles_null_values(): void
    {
        $objectWithNulls = new class {
            use Arrayable;
            
            private ?string $nullString = null;
            private ?int $nullInt = null;
            private ?array $nullArray = null;
        };

        $result = $objectWithNulls->toArray();

        $this->assertIsArray($result);
        $this->assertNull($result['nullString']);
        $this->assertNull($result['nullInt']);
        $this->assertNull($result['nullArray']);
        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_handles_complex_data_types(): void
    {
        $complexObject = new class {
            use Arrayable;
            
            private array $numbers = [1, 2, 3];
            private \stdClass $object;
            private string $json = '{"test": "value"}';
            
            public function __construct()
            {
                $this->object = new \stdClass();
                $this->object->property = 'test value';
            }
        };

        $result = $complexObject->toArray();

        $this->assertIsArray($result);
        $this->assertSame([1, 2, 3], $result['numbers']);
        $this->assertInstanceOf(\stdClass::class, $result['object']);
        $this->assertSame('test value', $result['object']->property);
        $this->assertSame('{"test": "value"}', $result['json']);
        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_preserves_property_order(): void
    {
        $orderedObject = new class {
            use Arrayable;
            
            private string $first = 'first';
            private string $second = 'second';
            private string $third = 'third';
        };

        $result = $orderedObject->toArray();

        $keys = array_keys($result);
        $this->assertSame(['first', 'second', 'third'], $keys);
    }
}