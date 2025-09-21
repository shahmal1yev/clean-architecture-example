<?php

namespace Onion\Presentation\HTTP\Exceptions;

use Onion\Presentation\HTTP\Interface\DTOInterface;
use Throwable;

class HTTPException extends \Exception implements \Throwable, DTOInterface
{
    public readonly string $id;

    public function __construct(
        string     $message = "An error occurred, please try again later",
        int        $code = 500,
        ?Throwable $previous = null,
        string     $id = 'unknownException',
    )
    {
        parent::__construct($message, $code, $previous);
        $this->id = (class_exists($id))
            ? basename(str_replace('\\', '/', $id))
            : $id;
    }

    public static function throw(
        string $message = "An error occurred, please try again later",
        int    $code = 500,
        string $id = 'unknownException',
    )
    {
        throw new static($message, $code, null, $id);
    }

    public function jsonSerialize(): array
    {
        $content = [
            'exception' => $this->id,
            'message' => $this->message,
        ];

        if (env('APP_DEBUG') === 'true' && $this->getPrevious()) {
            $content['trace'] = $this->getPrevious()->getTrace();
        }

        return $content;
    }
}
