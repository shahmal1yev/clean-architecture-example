<?php

namespace Onion\Presentation\HTTP\Exceptions;

final class NotFoundException extends HTTPException
{
    public static function throw(
        string $message = "Resource not found",
        int $code = 404,
        string $id = self::class,
    )
    {
        throw new self($message, $code, id: $id);
    }
}
