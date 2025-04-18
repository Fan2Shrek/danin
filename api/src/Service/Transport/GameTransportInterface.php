<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;

interface GameTransportInterface
{
    public function send(string|Connection $connection, string $data, string $type): void;
}
