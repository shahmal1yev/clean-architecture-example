<?php

namespace Onion\Infrastructure\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Factory for creating and configuring the DI Container
 * 
 * This centralizes container creation logic and allows for consistent
 * configuration across different entry points (web, CLI, tests)
 */
final class ContainerFactory
{
    public static function create(?string $configPath = null): ContainerBuilder
    {
        $configPath = $configPath ?? __DIR__ . '/../../../config';
        
        $container = new ContainerBuilder();
        
        // Add our custom compiler passes
        $container->addCompilerPass(new RepositoryCompilerPass());
        
        // Load service configuration
        $loader = new YamlFileLoader($container, new FileLocator($configPath));
        $loader->load('services.yml');
        
        // Compile the container
        $container->compile();
        
        return $container;
    }
}
