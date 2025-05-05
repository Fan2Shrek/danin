<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

final readonly class StartRoomCommand
{
    public function __construct(
        public string $host,
        public int $port,
    ) {
    }
}
