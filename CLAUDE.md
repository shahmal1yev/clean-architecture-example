# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Testing
- Run all tests: `composer exec phpunit` or `vendor/bin/phpunit`
- Run specific test file: `vendor/bin/phpunit tests/Unit/Domains/BookTest.php`
- Test configuration is in `phpunit.xml` with strict settings enabled

### Dependencies
- Install dependencies: `composer install`
- Update dependencies: `composer update`

## Architecture

This is a PHP application implementing **Onion Architecture** (Clean Architecture) with clear separation of concerns:

### Layer Structure
- **Domain Layer** (`src/Domain/`): Core business entities, interfaces, and domain logic
  - `Entities/`: Domain entities like `Book` with immutable properties
  - `Repositories/`: Repository interfaces (e.g., `BookRepositoryInterface`)
  
- **Application Layer** (`src/App/`): Use cases and application services
  - `Services/`: Application services like `BookService` orchestrating domain operations
  
- **Infrastructure Layer** (`src/Infrastructure/`): External concerns and implementations
  - `Repositories/`: Concrete repository implementations with PDO
  - `Config/`: Configuration classes like `DatabaseCredentials`
  - `Factory/`: Factory classes for external dependencies

### Dependency Injection
- Uses Symfony DI Container configured in `config/services.yml`
- Services auto-wired and auto-configured by default
- Repository interfaces bound to concrete implementations
- Database credentials injected via constructor parameters

### Database
- PostgreSQL database with PDO connections
- Database credentials configured in services.yml
- Transactions handled in repository save operations

### Testing
- Base `TestCase` class sets up DI container for integration tests
- Separate test suites for Unit and Feature tests
- Tests use the same DI configuration as the application