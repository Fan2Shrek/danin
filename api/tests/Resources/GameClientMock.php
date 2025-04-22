<?php

declare(strict_types=1);

namespace App\Tests\Resources;

use App\Domain\Model\Connection;
use App\Service\Transport\GameTransportInterface;

final class GameClientMock implements GameTransportInterface
{
    private array $messages = [];

    public function __construct(
        private bool $echo = false,
    ) {
    }

    public function send(string|Connection $connection, string $message, string $type): void
    {
        if ($this->echo) {
            echo "Sending message: $message to connection: $connection with type: $type\n";
        }

        $this->messages[] = [
            'connection' => $connection,
            'message' => $message,
            'type' => $type,
        ];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
