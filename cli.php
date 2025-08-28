<?php

use Onion\Presentation\Adapters\CliBookManagementAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once './vendor/autoload.php';

// Bootstrap the same DI Container as HTTP
$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__."/config"));
$loader->load('services.yml');
$container->compile();

// CLI-specific adapter (different validation rules)
$cliAdapter = $container->get(CliBookManagementAdapter::class);

// Simple CLI command processing
if ($argc < 2) {
    echo "Usage: php cli.php [create|find] [args...]\n";
    exit(1);
}

$command = $argv[1];

try {
    switch ($command) {
        case 'create':
            if ($argc < 4) {
                echo "Usage: php cli.php create <name> <author>\n";
                exit(1);
            }
            
            $book = $cliAdapter->createBook([
                'name' => $argv[2],
                'author' => $argv[3]
            ]);
            
            echo "Book created successfully!\n";
            echo "ID: {$book->getId()}\n";
            echo "Name: {$book->getName()}\n";
            echo "Author: {$book->getAuthor()}\n";
            break;
            
        case 'find':
            if ($argc < 3) {
                echo "Usage: php cli.php find <id>\n";
                exit(1);
            }
            
            $book = $cliAdapter->findBook((int)$argv[2]);
            
            echo "Book found:\n";
            echo "ID: {$book->getId()}\n";
            echo "Name: {$book->getName()}\n";
            echo "Author: {$book->getAuthor()}\n";
            echo "Created: {$book->getCreatedAt()?->format('Y-m-d H:i:s')}\n";
            break;
            
        default:
            echo "Unknown command: $command\n";
            echo "Available commands: create, find\n";
            exit(1);
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit(1);
}