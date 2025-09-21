# Onion (PHP) — Clean/Onion Architecture Example

Simple Books API demonstrating Clean Architecture in PHP 8.4 with Symfony components and Doctrine ORM. 
It highlights strict layering, DI wiring, DTOs, and basic validation.

## Setup
1) Install dependencies

```shell
composer install
```

2) Configure environment

- Copy `.env.example` to `.env` and adjust values:

```shell
cp .env.example .env
```

- Ensure `APP_URL` points to where you’ll run the dev server (e.g. `http://localhost:8000`).
- Set DB_* variables (driver, host, dbname, user, password) if needed.

3) Create database schema (run migrations)

```shell
vendor/bin/doctrine-migrations migrate --configuration=migrations.yml -n
```

## Testing

Run the full test suite:

```shell
php saw test
```

## Running

Start the development server:

```shell
composer serve
```

This runs a small helper (`php saw serve`) that serves the project from the repo root. By default it uses `APP_URL` from `.env`. You can override host/port:

```shell
php saw serve --host=localhost --port=8000
```

## cURL examples

```shell
# Create
curl -i \
  -H "Content-Type: application/json" \
  -d '{"title":"Clean Architecture","author":"Robert C. Martin","description":"A book about software architecture."}' \
  http://localhost:8000/books
```

```shell
# List
curl -s "http://localhost:8000/books?page=1&size=2" | jq
```

```shell
# Read
curl -s "http://localhost:8000/books/1" | jq
```

## API
Base URL: `${APP_URL}` (e.g., `http://localhost:8000`)

- GET `/books`
  - Query params: `page` (int, default 1), `size` (int, default 10)
  - Response: paginated list with `data`, `meta`, and `links`

- GET `/books/{id}`
  - Path params: `id` (int)
  - Response: `{ data: { id, title, author, description, created_at, updated_at } }`

- POST `/books`
  - Body (JSON):
    ```json
    {
      "title": "Clean Architecture",
      "author": "Robert C. Martin",
      "description": "A book about software architecture."
    }
    ```
  - Validations:
    - `title`: required, 3..255 chars
    - `author`: required, 10..255 chars
    - `description`: required, 10..255 chars
  - Response: `201 Created` with `Location` header and `data` payload

## Notes on Architecture
- Domain: `BookInterface` defines the contract; domain rules live here and are framework-agnostic.
- Application: Use cases (`CreateBook`, `ReadBook`, `ListBooks`) orchestrate domain behavior via repository interfaces.
- Infrastructure: Doctrine entity `Infrastructure\Entities\Book` implements domain contract; repository persists and queries via `EntityManager`.
- Presentation: `HTTPAdapter` handles requests, input validation, DTO serialization, and HTTP errors.
- DI: `ContainerFactory` wires implementations using compiler passes and `config/services.yaml`.

## Troubleshooting
- 404s on routes while using the PHP built‑in server: try running `php -S localhost:8000 index.php` so all requests are routed through the front controller.
- DB connection errors: verify `migrations-db.php` and your database service are reachable; ensure the same credentials match those expected by Doctrine.
- Missing vendor binaries: ensure `composer install` ran successfully.
