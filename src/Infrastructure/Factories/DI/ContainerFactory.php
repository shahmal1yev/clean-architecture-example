<?php

declare(strict_types=1);

namespace Onion\Infrastructure\Factories\DI;

use Exception;
use Onion\App\Interfaces\UseCases\CreateBookInterface;
use Onion\App\Interfaces\UseCases\ListBooksInterface;
use Onion\App\Interfaces\UseCases\ReadBookInterface;
use Onion\App\UseCases\CreateBook;
use Onion\App\UseCases\ListBooks;
use Onion\App\UseCases\ReadBook;
use Onion\Domain\Repositories\BookRepositoryInterface;
use Onion\Infrastructure\CompilerPass\RepositoryCompilerPass;
use Onion\Infrastructure\CompilerPass\UseCaseCompilerPass;
use Onion\Infrastructure\Repositories\BookRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContainerFactory
{
    private static ?ContainerInterface $container = null;

    private function __construct()
    {}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new \BadMethodCallException("Cannot unserialize a singleton");
    }

    public function __clone(): void
    {
        throw new \BadMethodCallException("Cannot clone a singleton");
    }

    /**
     * @throws Exception
     */
    public static function getInstance(?string $path = null): ContainerInterface
    {
        if (!isset(self::$container)) {
            self::$container = self::configure($path);
        }

        return self::$container;
    }

    public static function reset(): void
    {
        self::$container = null;
    }

    /**
     * @throws Exception
     */
    private static function configure(?string $path = null): ContainerInterface
    {
        $path ??= __DIR__ . '/../../../../config';
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->setParameter('app.use_case_map', [
            ListBooksInterface::class => ListBooks::class,
            ReadBookInterface::class => ReadBook::class,
            CreateBookInterface::class => CreateBook::class,
        ]);

        $containerBuilder->setParameter('app.repository_map', [
            BookRepositoryInterface::class => BookRepository::class,
        ]);

        $loader = new YamlFileLoader($containerBuilder, new FileLocator($path));
        $loader->load('services.yaml');

        $containerBuilder->addCompilerPass(new UseCaseCompilerPass());
        $containerBuilder->addCompilerPass(new RepositoryCompilerPass());

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
