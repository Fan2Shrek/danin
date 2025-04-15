<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;

interface GameTransportInterface
{
    public function send(Connection $connection, string $data): void;
}
