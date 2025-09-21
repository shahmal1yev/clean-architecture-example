<?php

function container(): \Symfony\Component\DependencyInjection\ContainerInterface {
    return \Onion\Infrastructure\Factories\DI\ContainerFactory::getInstance();
}

function env(string $key, mixed $default = null): mixed {
    $value = getenv($key);

    if (false === $value) {
        $value = $_ENV[$key] ?? $default;
    }

    return $value;
}
