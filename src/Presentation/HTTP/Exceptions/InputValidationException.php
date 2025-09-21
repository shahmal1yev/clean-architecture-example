<?php

namespace Onion\Presentation\HTTP\Exceptions;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InputValidationException extends HTTPException
{
    public private(set) array $errors = [];

    public function __construct(array $errors = [])
    {
        parent::__construct(
            "Inputs validation failed",
            422,
            null,
            self::class,
        );
        $this->errors = $errors;
    }

    /**
     * @throws InputValidationException
     */
    public static function throw(
        string $message = "Inputs validation failed",
        int $code = 422,
        string $id = self::class,
        array $errors = []
    )
    {
        throw new self($errors);
    }

    public static function fromViolations(ConstraintViolationListInterface $violations): self
    {
        $errors = array_merge(...array_map(function (ConstraintViolation $violation) {
            $key = trim(str_replace("][", ".", $violation->getPropertyPath()), "[]");
            $value = $violation->getMessage();

            return [$key => $value];
        }, iterator_to_array($violations)));

        return new self($errors);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'errors' => $this->errors,
        ]);
    }
}
