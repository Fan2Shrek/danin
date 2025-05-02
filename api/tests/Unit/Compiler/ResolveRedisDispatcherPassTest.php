<?php

declare(strict_types=1);

namespace App\Tests\Unit\Compiler;

use App\DependencyInjection\Compiler\ResolveRedisDispatcherPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ResolveRedisDispatcherPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $redisDispatcher = new Definition();
        $container->setDefinition('redis.event_dispatcher', $redisDispatcher);
        $testService = new Definition()->addTag('redis.dispatcher');
        $container->setDefinition('test', $testService);

        $pass = new ResolveRedisDispatcherPass();
        $pass->process($container);

        self::assertTrue($container->has('redis.event_dispatcher'));
        self::assertArrayHasKey(EventDispatcherInterface::class, $testService->getBindings());
        self::assertSame(
            'redis.event_dispatcher',
            (string) $testService->getBindings()[EventDispatcherInterface::class]->getValues()[0],
        );
    }

    public function testProcessWithoutDispatcher(): void
    {
        $container = new ContainerBuilder();
        $testService = new Definition()->addTag('redis.dispatcher', ['required' => true]);
        $container->setDefinition('test', $testService);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Service "test" requires a Redis event dispatcher, but none is registered.');

        $pass = new ResolveRedisDispatcherPass();
        $pass->process($container);
    }

    public function testProcessWithOptionalDispatcher(): void
    {
        $container = new ContainerBuilder();
        $testService = new Definition()->addTag('redis.dispatcher', ['required' => false]);
        $container->setDefinition('test', $testService);

        $pass = new ResolveRedisDispatcherPass();
        $pass->process($container);

        self::assertFalse($container->has('redis.event_dispatcher'));
        self::assertArrayHasKey(EventDispatcherInterface::class, $testService->getBindings());
        self::assertSame(
            'event_dispatcher',
            (string) $testService->getBindings()[EventDispatcherInterface::class]->getValues()[0],
        );
    }

    public function testProcessWithNoTags(): void
    {
        $container = new ContainerBuilder();
        $testService = new Definition();
        $container->setDefinition('test', $testService);

        $pass = new ResolveRedisDispatcherPass();
        $pass->process($container);

        self::assertFalse($container->has('redis.event_dispatcher'));
        self::assertArrayNotHasKey(EventDispatcherInterface::class, $testService->getBindings());
    }
}
