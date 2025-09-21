<?php

declare(strict_types=1);

namespace Onion\Presentation\HTTP\Interface;

interface DTOInterface extends \JsonSerializable
{
    public function jsonSerialize(): array;
}
