<?php

use Onion\Presentation\Http\Request;
use Onion\Presentation\Routing\Router;
use Onion\Presentation\Actions\CreateBookAction;
use Onion\Presentation\Actions\CreateMultipleBooksAction;
use Onion\Presentation\Actions\FindBookAction;
use Onion\Presentation\Actions\SearchBooksAction;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once './vendor/autoload.php';

// Bootstrap DI Container
$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__."/config"));
$loader->load('services.yml');
$container->compile();

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
