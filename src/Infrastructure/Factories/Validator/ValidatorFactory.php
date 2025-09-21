<?php

namespace Onion\Infrastructure\Factories\Validator;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorFactory
{
    public function create(): ValidatorInterface
    {
        return Validation::createValidator();
    }
}
