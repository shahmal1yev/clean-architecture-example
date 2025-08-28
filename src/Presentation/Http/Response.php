<?php

namespace Onion\Presentation\Http;

/**
 * HTTP Response abstraction
 * 
 * Base class for all HTTP responses, providing a clean interface
 * for setting status codes, headers, and content.
 */
abstract class Response
{
    protected array $headers = [];

    public function __construct(
        protected int $statusCode = 200
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withHeader(string $name, string $value): static
    {
        $new = clone $this;
        $new->headers[$name] = $value;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    abstract public function getContent(): string;

    /**
     * Send the response to the client
     */
    public function send(): void
    {
        // Set status code
        http_response_code($this->statusCode);

        // Set headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Output content
        echo $this->getContent();
    }
}