<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;
use App\Service\Redis\Attribute\UseRedisDispatcher;
use App\Service\Redis\EventDispatcher\RedisEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[UseRedisDispatcher(true)]
class WorkerTransport implements GameTransportInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function send(string|Connection $connection, string $message, string $type): void
    {
        $this->eventDispatcher->dispatch(
            new RedisEvent([
                'type' => $type,
                'connection' => \is_string($connection) ? $connection : $connection->id,
                'content' => $message,
            ]),
            'tchat_message',
        );
    }
}
