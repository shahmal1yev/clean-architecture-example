<?php

namespace Onion\Presentation\Adapters;

use Onion\App\Interfaces\UseCases\CreateBookInterface;
use Onion\App\Interfaces\UseCases\ListBooksInterface;
use Onion\App\Interfaces\UseCases\ReadBookInterface;
use Onion\Presentation\HTTP\DTO\Book\BookCreatedDTO;
use Onion\Presentation\HTTP\DTO\Book\BookListDTO;
use Onion\Presentation\HTTP\DTO\Book\ReadBookDTO;
use Onion\Presentation\HTTP\Exceptions\HTTPException;
use Onion\Presentation\HTTP\Exceptions\InputValidationException;
use Onion\Presentation\HTTP\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class HTTPAdapter
{
    public function __construct(
        private ValidatorInterface $validator,
    )
    {
    }

    /**
     * @throws InputValidationException
     * @throws HTTPException
     */
    public function create(Request $request): Response
    {
        $validator = $this->validator;

        $data = json_decode($request->getContent(), true, 512) ?? [];

        $violations = $validator->validate($data, new Collection([
            'title' => [new NotBlank(), new Length(['max' => 255, 'min' => '3'])],
            'author' => [new NotBlank(), new Length(['max' => 255, 'min' => '10'])],
            'description' => [new NotBlank(), new Length(['max' => 255, 'min' => '10'])],
        ]));

        if ($violations->count() > 0) {
            throw InputValidationException::fromViolations($violations);
        }

        try {
            $book = container()->get(CreateBookInterface::class)->execute($data);
            $dto = new BookCreatedDTO($book);
        } catch (\Throwable $exception) {
            throw new HTTPException(previous: $exception);
        }

        return new Response(
            json_encode(['message' => 'Book created successfully', 'data' => $dto]),
            201,
            headers: [
                'Content-Type' => 'application/json',
                'Location' => $request->getSchemeAndHttpHost() . "/books/$book->id",
            ]
        );
    }

    /**
     * @throws NotFoundException
     * @throws HTTPException
     */
    public function read(Request $request): Response
    {
        $id = (int)$request->attributes->get('id');

        try {
            $book = container()->get(ReadBookInterface::class)->execute($id);
            $dto = new ReadBookDTO($book);

            return new JsonResponse(
                $dto,
                200,
                headers: ['Content-Type' => 'application/json']
            );
        } catch (\Onion\Domain\Exceptions\Book\BookNotFoundException $exception) {
            NotFoundException::throw('Book not found');
        } catch (\Throwable $exception) {
            throw new HTTPException(previous: $exception);
        }
    }

    /**
     * @throws InputValidationException
     */
    public function list(Request $request): Response
    {
        $violations = $this->validator->validate($request->query->all(), new Collection([
            'page' => [
                new \Symfony\Component\Validator\Constraints\Optional([
                    new \Symfony\Component\Validator\Constraints\Type('digit'),
                ]),
            ],
            'size' => [
                new \Symfony\Component\Validator\Constraints\Optional([
                    new \Symfony\Component\Validator\Constraints\Type('digit'),
                ]),
            ],
        ]));

        if (0 < $violations->count()) {
            throw InputValidationException::fromViolations($violations);
        }

        $params = [
            $request->query->get('page', 1),
            $request->query->get('size', 10)
        ];

        $data = container()->get(ListBooksInterface::class)->execute(...$params);
        $dto = new BookListDTO($data['items'], $data['page'], $data['per_page'], $data['total']);

        return new JsonResponse($dto, 200);
    }
}
