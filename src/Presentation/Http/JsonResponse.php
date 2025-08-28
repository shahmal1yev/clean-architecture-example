<?php

namespace Onion\Presentation\Http;

/**
 * JSON HTTP Response
 * 
 * Specialized response for JSON API endpoints with proper
 * content-type headers and JSON encoding.
 */
class JsonResponse extends Response
{
    public function __construct(
        private readonly array $data,
        int $statusCode = 200
    ) {
        parent::__construct($statusCode);
        $this->headers['Content-Type'] = 'application/json';
    }

    public function getContent(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    public function getData(): array
    {
        return $this->data;
    }
}