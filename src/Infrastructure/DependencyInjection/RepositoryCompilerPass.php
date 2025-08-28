<?php

namespace Onion\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Auto-registers repository interfaces with their implementations
 * 
 * Convention:
 * - Domain\Repositories\{Entity}RepositoryInterface 
 * - Infrastructure\Repositories\{Entity}Repository
 * - Infrastructure\Factory\Repositories\{Entity}RepositoryFactory
 */
final class RepositoryCompilerPass implements CompilerPassInterface
{
    private const string DOMAIN_REPOSITORY_NAMESPACE = 'Onion\Domain\Repositories\\';
    private const string INFRASTRUCTURE_REPOSITORY_NAMESPACE = 'Onion\Infrastructure\Repositories\\';
    private const string FACTORY_NAMESPACE = 'Onion\Infrastructure\Factory\Repositories\\';

    public function process(ContainerBuilder $container): void
    {
        // Find all repository interface definitions
        $repositoryInterfaces = $this->findRepositoryInterfaces($container);
        
        foreach ($repositoryInterfaces as $interfaceClass) {
            $this->registerRepositoryBinding($container, $interfaceClass);
        }
    }

    /**
     * @return string[]
     */
    private function findRepositoryInterfaces(ContainerBuilder $container): array
    {
        $interfaces = [];
        $repositoryDir = __DIR__ . '/../../Domain/Repositories';
        
        if (!is_dir($repositoryDir)) {
            return $interfaces;
        }
        
        // Scan the domain repositories directory for interface files
        $files = glob($repositoryDir . '/*Interface.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $fullClassName = self::DOMAIN_REPOSITORY_NAMESPACE . $className;
            $interfaces[] = $fullClassName;
        }
        
        return $interfaces;
    }

    private function registerRepositoryBinding(ContainerBuilder $container, string $interfaceClass): void
    {
        $entityName = $this->extractEntityName($interfaceClass);
        $repositoryClass = self::INFRASTRUCTURE_REPOSITORY_NAMESPACE . $entityName . 'Repository';
        $factoryClass = self::FACTORY_NAMESPACE . $entityName . 'RepositoryFactory';

        // Check if the implementation classes exist
        if (!class_exists($repositoryClass) || !class_exists($factoryClass)) {
            return; // Skip if implementation doesn't exist
        }

        // Register the repository with factory
        $repositoryDefinition = new Definition($repositoryClass);
        $repositoryDefinition->setFactory([new Reference($factoryClass), 'create']);
        $container->setDefinition($repositoryClass, $repositoryDefinition);

        // Create alias from interface to implementation
        $container->setAlias($interfaceClass, $repositoryClass)
                  ->setPublic(true); // Make public for testing
    }

    private function extractEntityName(string $interfaceClass): string
    {
        $shortName = substr($interfaceClass, strrpos($interfaceClass, '\\') + 1);
        return str_replace('RepositoryInterface', '', $shortName);
    }
}
