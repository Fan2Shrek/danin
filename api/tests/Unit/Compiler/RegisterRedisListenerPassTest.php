<?php

declare(strict_types=1);

namespace App\Tests\Unit\Compiler;

use App\DependencyInjection\Compiler\RegisterRedisListenerPass;
use App\Service\Redis\Attribute\AsRedisListener;
use App\Service\Redis\EventDispatcher\RedisEventDispatcher;
use App\Service\Redis\EventDispatcher\RedisListenerManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterRedisListenerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $listener = new Definition()->addTag('redis.listener', [
            'event' => 'event_name',
            'method' => 'method_name',
        ]);

        $container->setDefinition('test_listener', $listener);
        $container->register(RedisEventDispatcher::class);
        $listenerManager = $container->register(RedisListenerManager::class);

        $pass = new RegisterRedisListenerPass();
        $pass->process($container);

        $expectedCalls = [
            [
                'addListener',
                [
                    'event_name',
                    [new ServiceClosureArgument(new Reference('test_listener')), 'method_name'],
                ],
            ],
        ];

        self::assertEquals(
            $expectedCalls,
            $listenerManager->getMethodCalls()
        );
    }

    public function testProcessWithoutDispatcher(): void
    {
        $container = new ContainerBuilder();
        $listener = new Definition()->addTag('redis.listener', [
            'event' => 'event_name',
            'method' => 'method_name',
        ]);

        $container->setDefinition('test_listener', $listener);
        $listenerManager = $container->register(RedisListenerManager::class);

        $pass = new RegisterRedisListenerPass();
        $pass->process($container);

        self::assertEquals(
            [],
            $listenerManager->getMethodCalls()
        );
    }

    public function testInvokeListener(): void
    {
        $container = new ContainerBuilder();
        $listener = new Definition(InvokeListener::class)->addTag('redis.listener', [
            'event' => 'event_name',
            'method' => '__invoke',
        ]);

        $container->setDefinition('test_listener', $listener);
        $container->register(RedisEventDispatcher::class);
        $listenerManager = $container->register(RedisListenerManager::class);

        $pass = new RegisterRedisListenerPass();
        $pass->process($container);

        $expectedCalls = [
            [
                'addListener',
                [
                    'event_name',
                    [new ServiceClosureArgument(new Reference('test_listener')), '__invoke'],
                ],
            ],
        ];

        self::assertEquals(
            $expectedCalls,
            $listenerManager->getMethodCalls()
        );
    }

    public function testNonInvokeListener(): void
    {
        $container = new ContainerBuilder();
        $listener = new Definition(NonInvokeListener::class)->addTag('redis.listener', [
            'event' => 'event_name',
            'method' => 'methodName',
        ]);

        $container->setDefinition('test_listener', $listener);
        $container->register(RedisEventDispatcher::class);
        $listenerManager = $container->register(RedisListenerManager::class);

        $pass = new RegisterRedisListenerPass();
        $pass->process($container);

        $expectedCalls = [
            [
                'addListener',
                [
                    'event_name',
                    [new ServiceClosureArgument(new Reference('test_listener')), 'methodName'],
                ],
            ],
        ];

        self::assertEquals(
            $expectedCalls,
            $listenerManager->getMethodCalls()
        );
    }

    public function testConfigureFromAttribute(): void
    {
        $reflector = new \ReflectionClass(InvokeListener::class);
        $child = new ChildDefinition('test');

        RegisterRedisListenerPass::configureFromAttribute($child, new AsRedisListener('event_name'), $reflector);

        self::assertTrue($child->hasTag('redis.listener'));
        self::assertEquals(
            [
                'event' => 'event_name',
                'method' => '__invoke',
            ],
            $child->getTag('redis.listener')[0]
        );
    }

    public function testConfigureFromAttributeWithMethod(): void
    {
        $reflector = new \ReflectionClass(NonInvokeListener::class);
        $child = new ChildDefinition('test');

        RegisterRedisListenerPass::configureFromAttribute($child, new AsRedisListener('event_name', 'methodName'), $reflector);

        self::assertTrue($child->hasTag('redis.listener'));
        self::assertEquals(
            [
                'event' => 'event_name',
                'method' => 'methodName',
            ],
            $child->getTag('redis.listener')[0]
        );
    }

    public function testConfigureFromAttributeWithInvalidMethod(): void
    {
        $reflector = new \ReflectionClass(NonInvokeListener::class);
        $child = new ChildDefinition('test');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Method "invalidMethod" not found in class "App\Tests\Unit\Compiler\NonInvokeListener"');

        RegisterRedisListenerPass::configureFromAttribute($child, new AsRedisListener('event_name', 'invalidMethod'), $reflector);
    }

    public function testConfigureFromAttributeWithMissingInvokeMethod(): void
    {
        $reflector = new \ReflectionClass(NonInvokeListener::class);
        $child = new ChildDefinition('test');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Method "__invoke" not found in class "App\Tests\Unit\Compiler\NonInvokeListener"');

        RegisterRedisListenerPass::configureFromAttribute($child, new AsRedisListener('event_name'), $reflector);
    }

    public function testConfigureFromAttributeWithMissingMethod(): void
    {
        $reflector = new \ReflectionClass(NonInvokeListener::class);
        $child = new ChildDefinition('test');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Method "nonMethod" not found in class "App\Tests\Unit\Compiler\NonInvokeListener"');

        RegisterRedisListenerPass::configureFromAttribute($child, new AsRedisListener('event_name', 'nonMethod'), $reflector);
    }
}

class InvokeListener
{
    public function __invoke(): void
    {
    }
}

class NonInvokeListener
{
    public function methodName(): void
    {
    }
}
