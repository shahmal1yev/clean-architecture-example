<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once './vendor/autoload.php';


$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__."/config"));
$loader->load('services.yml');

$container->compile();
