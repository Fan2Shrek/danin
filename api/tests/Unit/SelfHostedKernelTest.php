<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DependencyInjection\Compiler\RemoveDefinitionPass;
use App\SelfHostedKernel;
use App\Util\FeatureManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SelfHostedKernelTest extends TestCase
{
    public function testCompilerPasses(): void
    {
        $kernel = new class('dev', true) extends SelfHostedKernel {
            public function build(ContainerBuilder $container): void
            {
                parent::build($container);
            }
        };

        $container = new ContainerBuilder();

        $kernel->build($container);

        self::assertCount(1, array_filter($container->getCompilerPassConfig()->getPasses(), fn ($pass) => \in_array(
            $pass::class,
            [
                RemoveDefinitionPass::class,
            ],
        )));
    }

    public function testBuildDefinition(): void
    {
        $kernel = new class('dev', true) extends SelfHostedKernel {
            public function build(ContainerBuilder $container): void
            {
                parent::build($container);
            }

            protected function getDisabledFeatures(): array
            {
                return ['test_feature'];
            }
        };

        $container = new ContainerBuilder();
        $container->register(FeatureManager::class);

        $kernel->build($container);

        self::assertSame(['test_feature'], $container->getDefinition(FeatureManager::class)->getArgument('$disabledFeatures'));
    }

    public function testProcess()
    {
        $kernel = new class('dev', true) extends SelfHostedKernel {
            protected const array SERVICE_IDS_REMOVE = [
                'test_service',
            ];
        };

        $container = new ContainerBuilder();
        $container->register('test_service');

        $kernel->process($container);

        self::assertSame([
            'container.remove' => [
                ['reason' => 'This service is not usable in self-hosted environments.'],
            ],
        ], $container->getDefinition('test_service')->getTags());
    }
}
