<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

return \Onion\Infrastructure\Factories\DI\ContainerFactory::getInstance();

