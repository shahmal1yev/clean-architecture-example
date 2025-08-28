<?php

namespace Tests;

use Onion\Infrastructure\DependencyInjection\ContainerFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static ContainerBuilder $container;

    public function setUp(): void
    {
        parent::setUp();

        if (!isset(self::$container)) {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            // Use the centralized container factory
            self::$container = ContainerFactory::create(__DIR__ . '/../config');
        }
    }
}
