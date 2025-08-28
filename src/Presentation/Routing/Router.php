<?php

namespace Onion\Presentation\Routing;

use Onion\Presentation\Http\JsonResponse;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple HTTP Router
 * 
 * This router demonstrates how to integrate Actions with your
 * existing dependency injection container while maintaining
 * architectural boundaries.
 */
class Router
{
    private array $routes = [];

    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * Register a route with HTTP method, pattern, and action class
     */
    public function addRoute(string $method, string $pattern, string $actionClass): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->compilePattern($pattern),
            'actionClass' => $actionClass,
            'originalPattern' => $pattern
        ];
    }

    /**
     * Match incoming request against registered routes
     */
    public function match(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = parse_url($request->getUri(), PHP_URL_PATH);


        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract path parameters from URL
                $pathParameters = $this->extractPathParameters($matches);
                $requestWithParams = $request->withPathParameters($pathParameters);

                // Resolve action from container and invoke
                $action = $this->container->get($route['actionClass']);
                return $action($requestWithParams);
            }
        }

        return $this->notFoundResponse();
    }

    /**
     * Compile URL pattern to regex (simple implementation)
     * Converts /books/{id} to regex pattern
     */
    private function compilePattern(string $pattern): string
    {
        $pattern = preg_quote($pattern, '/');
        $pattern = str_replace('\{([^}]+)\}', '(?P<$1>[^/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }

    private function extractPathParameters(array $matches): array
    {
        $parameters = [];
        
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $parameters[$key] = $value;
            }
        }
        
        return $parameters;
    }

    private function notFoundResponse(): Response
    {
        return new JsonResponse([
            'success' => false,
            'error' => 'Not Found',
            'message' => 'The requested resource was not found'
        ], 404);
    }
}
