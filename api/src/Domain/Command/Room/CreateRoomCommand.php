<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

final readonly class CreateRoomCommand
{
    public function __construct(
        public string $game,
        public string $transport,
        public array $commands = [],
        public array $config = [],
    ) {
    }
}
