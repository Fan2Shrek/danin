<?php

namespace App;

use App\DependencyInjection\Compiler\RegisterRedisListenerPass;
use App\DependencyInjection\Compiler\ResolveRedisDispatcherPass;
use App\Service\Redis\Attribute\AsRedisListener;
use App\Service\Redis\Attribute\UseRedisDispatcher;
use App\Service\Redis\EventDispatcher\RedisEventDispatcher;
use App\Service\Redis\RedisListenerManager;
use App\Service\Transport\GameTransport;
use App\Service\Transport\GameTransportInterface;
use App\Service\Transport\WorkerTransport;
use App\Service\Worker\DaninWorker;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private bool $appDebug = true;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ResolveRedisDispatcherPass());
        $container->addCompilerPass(new RegisterRedisListenerPass());

        if ('prod' === $this->getEnvironment() || $this->appDebug) {
            $this->buildAsProd($container);
        }

        $container->setDefinition('app.game.transport', new Definition(GameTransport::class));

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
        $worker = new Definition(DaninWorker::class);
        $worker->setAutowired(true);
        $worker->setArgument('$gameTransport', new Reference('app.game.transport'));

        $container->setAlias(GameTransportInterface::class, WorkerTransport::class);
        $container->setDefinition(DaninWorker::class, $worker);
    }
}
