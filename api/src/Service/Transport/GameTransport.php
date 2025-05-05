<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;
use Psr\Log\LoggerInterface;

class GameTransport implements GameTransportInterface
{
    public function __construct(
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

        if (!str_ends_with($message, "\n")) {
            $message .= "\n";
        }

        $connection->connect();
        $connection->send($message);
    }
}
