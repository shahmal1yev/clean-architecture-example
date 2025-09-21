<?php

namespace Onion\Infrastructure\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UseCaseCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('app.use_case_map')) {
            return;
        }

        $map = $container->getParameter('app.use_case_map');

        foreach ($map as $interface => $implementation) {
            if (!$container->hasDefinition($implementation) && !$container->hasAlias($implementation)) {
                $container->register($implementation, $implementation)
                    ->setAutowired(true)
                    ->setPublic(false);
            }

            $container->setAlias($interface, $implementation)->setPublic(true);
        }
    }
}
