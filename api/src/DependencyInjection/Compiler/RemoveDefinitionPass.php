<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('container.remove') as $serviceId => $tags) {
            $container->removeDefinition($serviceId);
            $container->log($this, \sprintf('Removed service definition "%s" (reason: %s)', $serviceId, implode(', ', array_map(
                static fn (array $tag): string => $tag['reason'] ?? 'no reason provided',
                $tags
            ))));
        }
    }
}
