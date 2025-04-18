<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Service\Redis\Attribute\AsRedisListener;
use App\Service\Redis\EventDispatcher\RedisEvent;
use App\Service\Worker\DaninWorker;
use App\Service\Worker\WorkerAction;
use Psr\Log\LoggerInterface;

#[AsRedisListener('tchat_message')]
final class ActionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private DaninWorker $worker,
    ) {
    }

    public function __invoke(RedisEvent $event): void
    {
        $this->logger->debug('Received event', [
            'event' => $event,
        ]);

        $this->worker->processAction(new WorkerAction(
            $event->data['type'],
            array_merge(
                ['id' => $event->data['connection']],
                json_decode($event->data['content'], true),
            )
        ));
    }
}
