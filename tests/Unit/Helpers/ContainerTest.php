<?php

namespace Tests\Unit\Helpers;

use Onion\Infrastructure\Factories\DI\ContainerFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\TestCase;

class ContainerTest extends TestCase
{
    public function test_container_helper_returns_container_instance(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, container());
    }

    public function test_container_is_singleton(): void
    {
        $this->assertSame(container(), container());
    }

    public function test_container_can_be_reset(): void
    {
        $actual = container();
        $expected = container();

        $this->assertSame($expected, $actual);
        ContainerFactory::reset();
        $this->assertNotSame($expected, container());
    }
}
