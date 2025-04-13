<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Model\Connection;

class GameTransport
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
