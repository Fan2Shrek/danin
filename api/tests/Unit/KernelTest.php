<?php

namespace App\Tests\Unit;

use App\DependencyInjection\Compiler\ResolveRedisDispatcherPass;
use App\DependencyInjection\Compiler\RegisterRedisListenerPass;;
use App\DaninKernel;
use App\Service\MessageProcessor;
use App\Service\Redis\Attribute\AsRedisListener;
use App\Service\Redis\Attribute\UseRedisDispatcher;
use App\Service\Transport\GameTransportInterface;
use App\Service\Transport\WorkerTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use App\Service\Worker\DaninWorker;

class KernelTest extends TestCase
{
    public function testCompilerPassesAreAdded()
    {
        $kernel = new class('dev', true) extends DaninKernel {
            public function build(ContainerBuilder $container): void
            {
                parent::build($container);
            }
        };
        $container = new ContainerBuilder();

        $kernel->build($container);

        self::assertCount(2, array_filter($container->getCompilerPassConfig()->getPasses(), fn ($pass) => \in_array(
            $pass::class,
            [
                ResolveRedisDispatcherPass::class,
                RegisterRedisListenerPass::class,
            ],
        )));
    }

    public function testBuildAsProdAddsWorkerDefinition()
    {
        $kernel = new class('prod', true) extends DaninKernel {
            public function build(ContainerBuilder $container): void
            {
                parent::build($container);
            }
        };
        $container = new ContainerBuilder();

        $kernel->build($container);

        self::assertSame(WorkerTransport::class, $container->getAlias(GameTransportInterface::class)->__toString());
    }

    public function testMocksRegistration()
    {
        $kernel = new class('worker', true) extends DaninKernel {
            public function build(ContainerBuilder $container): void
            {
                $this->useMocks = true;
                $this->appDebug = true;
                parent::build($container);
            }
        };

        $container = new ContainerBuilder();

        $kernel->build($container);

        self::assertTrue($container->has(GameTransportInterface::class));
        self::assertTrue($container->has(DaninWorker::class));
        self::assertSame('app.game.client.mock', $container->getDefinition(DaninWorker::class)->getArgument('$gameTransport')->__toString());
        self::assertSame(WorkerTransport::class, $container->getDefinition(MessageProcessor::class)->getArgument('$transport')->__toString());
    }

    public function testAutoconfigurationAttributesAreRegistered()
    {
        $kernel = new class('worker', true) extends DaninKernel {
            public function build(ContainerBuilder $container): void
            {
                $this->useMocks = true;
                $this->appDebug = true;
                parent::build($container);
            }
        };

        $container = new ContainerBuilder();

        $kernel->build($container);

        self::assertArrayHasKey(UseRedisDispatcher::class, $container->getAutoconfiguredAttributes());
        self::assertArrayHasKey(AsRedisListener::class, $container->getAutoconfiguredAttributes());
    }
}
