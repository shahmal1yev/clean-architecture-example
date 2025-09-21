<?php

namespace Onion\App\Interfaces\UseCases;

use Onion\Domain\Entities\BookInterface;

interface CreateBookInterface
{
    public function __invoke(array $data): BookInterface;
    public function execute(array $data): BookInterface;
}
