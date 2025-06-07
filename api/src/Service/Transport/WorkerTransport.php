<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Entity\RoomConfig;
use App\Service\Redis\Attribute\UseRedisDispatcher;
use App\Service\Redis\EventDispatcher\RedisEvent;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[UseRedisDispatcher(true)]
class WorkerTransport implements GameTransportInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function send(RoomConfig $roomConfig, string $message, string $type): void
    {
        $this->eventDispatcher->dispatch(
            new RedisEvent([
                'type' => $type,
                'connection' => (string) $roomConfig->getRoom()->getId(),
                'content' => $message,
            ]),
            'tchat_message',
        );
    }
}
