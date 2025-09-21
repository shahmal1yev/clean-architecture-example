<?php

ini_set('display_errors', 1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require_once __DIR__ . '/vendor/autoload.php';
$container = require_once __DIR__ . '/bootstrap.php';
$routes = require_once __DIR__ . '/routes/api.php';

$request = Request::createFromGlobals();
$context = new RequestContext()->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);
$httpAdapter = \Onion\Presentation\Adapters\HTTPAdapter::class;

$newResponse = fn(\Onion\Presentation\HTTP\Exceptions\HTTPException $e) => new Response(
    json_encode($e),
    $e->getCode(),
    ['Content-Type' => 'application/json']
);

try {
    $attributes = $matcher->match($request->getPathInfo());
} catch (ResourceNotFoundException|MethodNotAllowedException $e) {
    return $newResponse(new \Onion\Presentation\HTTP\Exceptions\HTTPException('Resource not found'))->send();
}

$routeName = $attributes['_route'];
$callable = $attributes['_controller'];
unset($attributes['_controller']);

foreach ($attributes as $name => $value) {
    $request->attributes->set($name, $value);
}

try {
    $adapter = container()->get(current($callable));
    $action = next($callable);

    $response = $adapter->$action($request);
} catch (\Onion\Presentation\HTTP\Exceptions\HTTPException $e) {
    $response = $newResponse($e);
} catch (Throwable $e) {
    $exception = new \Onion\Presentation\HTTP\Exceptions\HTTPException(previous: $e);
    $response = $newResponse($exception);
}

$response->send();
