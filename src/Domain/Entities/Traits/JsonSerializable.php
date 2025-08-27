<?php

namespace Onion\Domain\Entities\Traits;

trait JsonSerializable
{
    public function jsonSerialize(): array
    {
        if ($this instanceof \Onion\Domain\Entities\Interfaces\Arrayable) {
            return $this->toArray();
        }

        throw new \RuntimeException("jsonSerialize is not implemented");
    }
}
