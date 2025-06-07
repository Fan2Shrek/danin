<?php

declare(strict_types=1);

namespace App\Tests\Resources;

use App\Entity\RoomConfig;
use App\Service\Transport\GameTransportInterface;

final class GameClientMock implements GameTransportInterface
{
    private array $messages = [];

    public function __construct(
        private bool $echo = false,
    ) {
    }

    public function send(RoomConfig $config, string $message, string $type): void
    {
        if ($this->echo) {
            echo "Sending message: $message to connection: {$config->getId()} with type: $type\n";
        }

        $this->messages[] = [
            'connection' => $config->getId(),
            'message' => $message,
            'type' => $type,
        ];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
