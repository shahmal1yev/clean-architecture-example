<?php

namespace Onion\App\Interfaces\UseCases;

use Onion\Domain\Entities\BookInterface;

interface ReadBookInterface
{
    public function __invoke(int $id): BookInterface;
    public function execute(int $id): BookInterface;
}
