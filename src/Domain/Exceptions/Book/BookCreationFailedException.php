<?php

namespace Onion\Domain\Exceptions\Book;

class BookCreationFailedException extends \Exception
{
    public function __construct(string $message = "Book creation failed", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
