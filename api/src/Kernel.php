<?php

namespace App;

use App\DependencyInjection\Compiler\RegisterRedisListenerPass;
use App\DependencyInjection\Compiler\ResolveRedisDispatcherPass;
use App\Service\MessageProcessor;
use App\Service\Redis\Attribute\AsRedisListener;
use App\Service\Redis\Attribute\UseRedisDispatcher;
use App\Service\Redis\EventDispatcher\RedisEventDispatcher;
use App\Service\Redis\EventDispatcher\RedisListenerManager;
use App\Service\Transport\GameTransport;
use App\Service\Transport\GameTransportInterface;
use App\Service\Transport\WorkerTransport;
use App\Service\Worker\DaninWorker;
use App\Tests\Resources\GameClientMock;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected bool $appDebug = false;
    protected bool $useMocks = false;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ResolveRedisDispatcherPass());
        $container->addCompilerPass(new RegisterRedisListenerPass());

        if (!$container->has(DaninWorker::class)) {
            $container->register(DaninWorker::class)->setAutowired(true);
        }

        if ('prod' === $this->getEnvironment() || $this->appDebug) {
            $this->buildAsProd($container);
        }

        if ($this->useMocks) {
            $this->registerMocks($container);
        }

        $container->setDefinition('app.game.transport', new Definition(GameTransport::class)->setAutowired(true));

        if (!$container->hasAlias(GameTransportInterface::class)) {
            $container->setAlias(GameTransportInterface::class, 'app.game.transport');
        }

        $listenerManager = new Definition(RedisListenerManager::class);
        $listenerManager->setAutowired(true);

        $container->setDefinition(
            'redis.listener_manager',
            $listenerManager,
        );

        $container->setAlias(RedisListenerManager::class, 'redis.listener_manager');
        $container->setAlias('redis.event_dispatcher', RedisEventDispatcher::class);

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
        $worker = $container->getDefinition(DaninWorker::class);
        $worker->setArgument('$gameTransport', new Reference('app.game.transport'));

        $container->setAlias(GameTransportInterface::class, WorkerTransport::class);
        $container->setDefinition(DaninWorker::class, $worker);
    }

    private function registerMocks(ContainerBuilder $container): void
    {
        $gameClientMock = new Definition(GameClientMock::class);
        $container->setDefinition('app.game.client.mock', $gameClientMock);
        $container->setAlias(GameTransportInterface::class, 'app.game.client.mock');

        if ($this->appDebug) {
            $container->register(MessageProcessor::class)
                ->setArgument('$transport', new Reference(WorkerTransport::class));
            $container->getDefinition(DaninWorker::class)
                ->setArgument('$gameTransport', new Reference('app.game.client.mock'));
        }
    }
}
