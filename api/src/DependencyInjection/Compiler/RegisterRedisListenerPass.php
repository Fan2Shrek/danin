<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Service\Redis\Attribute\AsRedisListener;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRedisListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('redis.event_dispatcher') || !$container->has('redis.listener_manager')) {
            return;
        }

        $listenerManager = $container->getDefinition('redis.listener_manager');

        foreach ($container->findTaggedServiceIds('redis.listener') as $id => $tags) {
            foreach ($tags as $tag) {
                $listenerManager->addMethodCall('addListener', [$tag['event'], [new ServiceClosureArgument(new Reference($id)), $tag['method']]]);
            }
        }
    }

    public static function configureFromAttribute(ChildDefinition $definition, AsRedisListener $attribute, \ReflectionClass $reflector): void
    {
        if (null !== $attribute->method && !$reflector->hasMethod($attribute->method)) {
            throw new \RuntimeException(\sprintf('Method "%s" not found in class "%s"', $attribute->method, $reflector->getName()));
        }

        if (null === $attribute->method && !$reflector->hasMethod('__invoke')) {
            throw new \RuntimeException(\sprintf('Method "__invoke" not found in class "%s"', $reflector->getName()));
        }

        $definition->addTag('redis.listener', [
            'event' => $attribute->event,
            'method' => $attribute->method ?? '__invoke',
        ]);
    }
}
