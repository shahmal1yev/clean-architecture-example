<?php

namespace Tests;

use Onion\Infrastructure\Factories\DI\ContainerFactory;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ContainerFactory::getInstance();
    }

    protected function tearDown(): void
    {
        ContainerFactory::reset();
        parent::tearDown();
    }
}
