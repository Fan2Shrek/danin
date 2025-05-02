<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Service\Redis\EventDispatcher\RedisEventDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ResolveRedisDispatcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('redis.dispatcher') as $id => $tags) {
            $isRequired = array_reduce($tags, static function (bool $carry, array $tag): bool {
                return $carry || ($tag['required'] ?? false);
            }, false);
            $definition = $container->getDefinition($id);
            $bindings = $definition->getBindings();

            if (!($hasRedis = $container->has(RedisEventDispatcher::class)) && $isRequired) {
                throw new \RuntimeException(\sprintf('Service "%s" requires a Redis event dispatcher, but none is registered.', $id));
            }

            $evId = $hasRedis ? RedisEventDispatcher::class : 'event_dispatcher';

            $bindings[EventDispatcherInterface::class] = new Reference($evId);
            $definition->setBindings($bindings);
        }
    }
}
