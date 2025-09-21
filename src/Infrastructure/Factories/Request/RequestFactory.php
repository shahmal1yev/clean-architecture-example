<?php

namespace Onion\Infrastructure\Factories\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestFactory
{
    public function create(): Request
    {
        return Request::createFromGlobals();
    }
}
