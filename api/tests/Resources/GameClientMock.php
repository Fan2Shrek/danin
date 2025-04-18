<?php

declare(strict_types=1);

namespace App\Tests\Resources;

use App\Domain\Model\Connection;
use App\Service\Transport\GameTransportInterface;

final class GameClientMock implements GameTransportInterface
{
    public function send(string|Connection $connection, string $message, string $type): void
    {
        echo "Sending message: $message to connection: $connection with type: $type\n";
    }
}
