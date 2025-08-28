<?php

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('display_errors', 0);
ini_set('error_log', __DIR__ . '/my_error.log');


use Onion\Infrastructure\DependencyInjection\ContainerFactory;
use Onion\Presentation\Actions\CreateBookAction;
use Onion\Presentation\Actions\CreateMultipleBooksAction;
use Onion\Presentation\Actions\FindBookAction;
use Onion\Presentation\Actions\SearchBooksAction;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Routing\Router;

require_once './vendor/autoload.php';

// Bootstrap DI Container
$container = ContainerFactory::create(__DIR__ . '/config');

// Create router and register routes
$router = new Router($container);

// Command routes (write operations)
$router->addRoute('POST', '/books', CreateBookAction::class);
$router->addRoute('POST', '/books/bulk', CreateMultipleBooksAction::class);

// Query routes (read operations)  
$router->addRoute('GET', '/books/search', SearchBooksAction::class);
$router->addRoute('GET', '/books/{id}', FindBookAction::class);

// Handle the HTTP request
try {
    $request = Request::fromGlobals();
    $response = $router->match($request);
    $response->send();
} catch (\Throwable $e) {
    // Global error handling
    $errorResponse = new \Onion\Presentation\Http\JsonResponse([
        'success' => false,
        'error' => 'Internal Server Error',
        'message' => 'An unexpected error occurred'
    ], 500);

    $errorResponse->send();

    // Log error in production
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
}
