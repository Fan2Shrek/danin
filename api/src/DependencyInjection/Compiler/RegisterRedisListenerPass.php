<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRedisListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('redis.event_dispatcher')) {
            return;
        }

        $listenerManager = $container->getDefinition('redis.listener_manager');

        foreach ($container->findTaggedServiceIds('redis.listener') as $id => $tags) {
            foreach ($tags as $tag) {
                $listenerManager->addMethodCall('addListener', [$tag['event'], [new ServiceClosureArgument(new Reference($id)), $tag['method']]]);
            }
        }
    }
}
