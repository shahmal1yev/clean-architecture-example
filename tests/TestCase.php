<?php

namespace Tests;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static ContainerBuilder $container;

    public function setUp(): void
    {
        parent::setUp();

        if (!isset(self::$container)) {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            self::$container = new ContainerBuilder();
            $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__ . '/../config'));
            $loader->load('services.yml');
            self::$container->compile();
        }
    }
}
