<?php

namespace Onion\Domain\Entities\Traits;

trait Arrayable
{
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);

        return array_merge(...array_map(
            fn (\ReflectionProperty $property) => [$property->getName() => $property->getValue($this)],
            $reflection->getProperties()
        ));
    }
}
