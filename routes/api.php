<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$defines = [
    'books_list' => [
        'route' => new \Symfony\Component\Routing\Route(
            '/books',
            defaults: ['_controller' => [\Onion\Presentation\Adapters\HTTPAdapter::class, 'list']],
            methods: [Request::METHOD_GET],),
    ],
    'books_read' => [
        'route' => new \Symfony\Component\Routing\Route(
            '/books/{id}',
            defaults: ['_controller' => [\Onion\Presentation\Adapters\HTTPAdapter::class, 'read']],
            requirements: ['id' => '\d{1,9}$'],
            methods: [Request::METHOD_GET]
        ),
    ],
    'books_create' => [
        'route' => new \Symfony\Component\Routing\Route(
            '/books',
            defaults: ['_controller' => [\Onion\Presentation\Adapters\HTTPAdapter::class, 'create']],
            methods: [Request::METHOD_POST],
        )
    ]
];

foreach ($defines as $name => $definition) {
    $routes->add($name, $definition['route']);
}

return $routes;
