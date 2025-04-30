<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;
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

    public function send(Connection $connection, string $message, string $type): void
    {
        $this->logger->debug('Sending "{message}" to connection with id "{id}"', [
            'connection' => $connection,
            'message' => $message,
            'type' => $type,
        ]);

        $this->eventDispatcher->dispatch(
            new RedisEvent([
                'type' => $type,
                'connection' => $connection->host,
                'content' => $message,
            ]),
            'tchat_message',
        );
    }
}
