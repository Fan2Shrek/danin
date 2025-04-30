<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Model\Connection;
use App\Domain\Model\Message;
use App\Service\Transport\GameTransportInterface;

final class MessageProcessor
{
    public function __construct(
        private ConnectionManager $connectionManager,
        private GameTransportInterface $transport,
    ) {
    }

    public function process(Message $message): void
    {
        // dev change to real handler
        $this->transport->send(new Connection('172.17.0.1', 0), json_encode(['type' => 'spawn', 'content' => 4]), 'message');

        // add to tchat
    }
}
