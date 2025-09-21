<?php

namespace Onion\Domain\Exceptions\Book;

class BookNotFoundException extends \Exception
{
    public function __construct(string $message = "Book not found", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
