# Dependency Injection Auto-Registration

This directory contains infrastructure for automating repository registration in the Symfony DI Container.

## Problem Solved

Previously, adding a new entity required manual DI container configuration for each repository:

```yaml
# Manual registration (OLD WAY - now automated)
Onion\Domain\Repositories\BookRepositoryInterface:
  alias: Onion\Infrastructure\Repositories\BookRepository
  public: true

Onion\Infrastructure\Repositories\BookRepository:
  class: Onion\Infrastructure\Repositories\BookRepository
  factory: ['@Onion\Infrastructure\Factory\Repositories\BookRepositoryFactory', 'create']
```

## Solution: Convention-Based Auto-Registration

The `RepositoryCompilerPass` automatically discovers and registers repositories based on naming conventions:

### Naming Convention

For an entity called `Book`:

- **Domain Interface**: `Onion\Domain\Repositories\BookRepositoryInterface`
- **Infrastructure Implementation**: `Onion\Infrastructure\Repositories\BookRepository`  
- **Factory**: `Onion\Infrastructure\Factory\Repositories\BookRepositoryFactory`

### How It Works

1. **Discovery**: Scans `src/Domain/Repositories/` for `*Interface.php` files
2. **Convention Mapping**: Maps interfaces to implementations using naming patterns
3. **Auto-Registration**: Creates service definitions and aliases automatically
4. **Factory Integration**: Automatically wires factory-based instantiation

### Adding New Entities

To add a new entity (e.g., `Author`), simply create:

1. `src/Domain/Entities/Author.php`
2. `src/Domain/Repositories/AuthorRepositoryInterface.php`
3. `src/Infrastructure/Repositories/AuthorRepository.php`
4. `src/Infrastructure/Factory/Repositories/AuthorRepositoryFactory.php`

**No DI container configuration needed!**

### Benefits

- **DRY Principle**: Eliminates boilerplate configuration
- **Consistency**: Enforces naming conventions across the codebase
- **Maintainability**: Reduces configuration drift as the codebase grows
- **Developer Experience**: Focus on domain logic, not plumbing

### Architecture Compliance

This solution maintains:
- **Dependency Inversion**: Interfaces remain in domain layer
- **Clean Architecture**: Layer boundaries are preserved
- **Factory Pattern**: Complex object creation stays separated
- **Testability**: All services remain mockable and testable

## Usage

The auto-registration is enabled by default through `ContainerFactory::create()`. No additional configuration is required.