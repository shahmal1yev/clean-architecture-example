<?php

namespace Onion\Presentation\GraphQL;

use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Onion\Presentation\Http\JsonResponse;
use Onion\Presentation\Http\Request;
use Onion\Presentation\Http\Response;

readonly class GraphQLController
{
    public function __construct(
        private Schema $schema
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $input = json_decode($request->getBody(), true, 512, JSON_THROW_ON_ERROR);
            
            if (!isset($input['query'])) {
                return new JsonResponse(['error' => 'Missing query'], 400);
            }
            
            $query = $input['query'];
            $variables = $input['variables'] ?? null;
            $operationName = $input['operationName'] ?? null;
            
            $result = GraphQL::executeQuery(
                schema: $this->schema,
                source: $query,
                variableValues: $variables,
                operationName: $operationName
            );
            
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
            
            return new JsonResponse($output, 200);
            
        } catch (\JsonException $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}