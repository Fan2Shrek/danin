<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Entity\RoomConfig;

interface GameTransportInterface
{
    public function send(RoomConfig $roomConfig, string $data, string $type): void;
}
