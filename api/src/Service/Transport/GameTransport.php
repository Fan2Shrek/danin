<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;
use App\Service\Transport\GameTransportInterface;

class GameTransport implements GameTransportInterface
{
    public function send(Connection $connection, string $message): void
    {
        if (!str_ends_with($message, "\n")) {
            $message .= "\n";
        }

        $connection->connect();
        $connection->send($message);
    }
}
