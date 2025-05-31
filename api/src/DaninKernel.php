<?php

namespace App;

use App\DependencyInjection\Compiler\RegisterRedisListenerPass;
use App\DependencyInjection\Compiler\ResolveRedisDispatcherPass;
use App\Service\Message\MessageProcessor;
use App\Service\Redis\Attribute\AsRedisListener;
use App\Service\Redis\Attribute\UseRedisDispatcher;
use App\Service\Transport\GameTransport;
use App\Service\Transport\GameTransportInterface;
use App\Service\Transport\WorkerTransport;
use App\Service\Worker\DaninWorker;
use App\Tests\Resources\GameClientMock;
use App\Tests\Resources\HubSpy;
use App\Util\FeatureManager;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Mercure\HubInterface;

class DaninKernel extends Kernel
{
    use MicroKernelTrait;

    protected bool $appDebug = true;
    protected bool $useMocks = false;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ResolveRedisDispatcherPass());
        $container->addCompilerPass(new RegisterRedisListenerPass());

        $container->register(FeatureManager::class);

        $container->register('kernel.get_feature', \Closure::class)
            ->setFactory([\Closure::class, 'fromCallable'])
            ->setArguments([
                [new Reference(FeatureManager::class), 'isEnable'],
            ])
            ->addTag('routing.expression_language_function', [
                'function' => 'is_enable',
            ])
        ;

        if ('prod' === $this->getEnvironment() || $this->appDebug) {
            $this->buildAsProd($container);

            if ('worker' === $this->getEnvironment()) {
                $container->setAlias(GameTransportInterface::class, GameTransport::class);
            }
        }

        if ('test' === $this->getEnvironment() || $this->useMocks) {
            $this->registerMocks($container);
        }

        $container->register(GameTransport::class)->setAutowired(true);

        if (!$container->hasAlias(GameTransportInterface::class)) {
            $container->setAlias(GameTransportInterface::class, GameTransport::class);
        }

        $container->registerAttributeForAutoconfiguration(
            UseRedisDispatcher::class,
            static function (ChildDefinition $definition, UseRedisDispatcher $attribute): void {
                $definition->addTag('redis.dispatcher', [
                    'throw_if_not_found' => $attribute->throwIfNotFound,
                ]);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsRedisListener::class,
            RegisterRedisListenerPass::configureFromAttribute(...),
        );
    }

    private function buildAsProd(ContainerBuilder $container): void
    {
        $container->setAlias(GameTransportInterface::class, WorkerTransport::class);
    }

    private function registerMocks(ContainerBuilder $container): void
    {
        $container->register(HubInterface::class)
            ->setClass(HubSpy::class)
        ;
        $container->register(GameClientMock::class);
        $container->setAlias(GameTransportInterface::class, GameClientMock::class);

        if ($this->appDebug) {
            $container->register(MessageProcessor::class)
                ->setArgument('$transport', new Reference(WorkerTransport::class));
            $container->register(DaninWorker::class)
                ->setArgument('$gameTransport', new Reference('app.game.client.mock'));
        }
    }
}
