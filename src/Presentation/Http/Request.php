<?php

namespace Onion\Presentation\Http;

/**
 * HTTP Request abstraction
 * 
 * This class provides a clean abstraction over PHP's superglobals
 * and makes the presentation layer testable and framework-agnostic.
 */
readonly class Request
{
    public function __construct(
        private string $method,
        private string $uri,
        private array $headers,
        private ?string $body,
        private array $pathParameters = [],
        private array $queryParameters = []
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            headers: self::extractHeaders(),
            body: file_get_contents('php://input') ?: null,
            queryParameters: $_GET
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? null;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getJsonData(): array
    {
        if ($this->body === null) {
            return [];
        }

        $data = json_decode($this->body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON in request body: ' . json_last_error_msg());
        }

        return $data ?? [];
    }

    public function getPathParameter(string $name): ?string
    {
        return $this->pathParameters[$name] ?? null;
    }

    public function getQueryParameter(string $name): ?string
    {
        return $this->queryParameters[$name] ?? null;
    }

    public function withPathParameters(array $parameters): self
    {
        return new self(
            method: $this->method,
            uri: $this->uri,
            headers: $this->headers,
            body: $this->body,
            pathParameters: $parameters,
            queryParameters: $this->queryParameters
        );
    }

    private static function extractHeaders(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerName] = $value;
            }
        }
        
        // Handle special case headers
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }
        
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
        }
        
        return $headers;
    }
}