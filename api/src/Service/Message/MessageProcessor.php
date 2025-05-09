<?php

declare(strict_types=1);

namespace App\Service\Message;

use App\Domain\Model\Connection;
use App\Domain\Model\Message;
use App\Service\ConnectionManager;
use App\Service\Message\Transformer\TransformerManager;
use App\Service\Transport\GameTransportInterface;

final class MessageProcessor
{
    public function __construct(
        private ConnectionManager $connectionManager,
        private GameTransportInterface $transport,
        private TransformerManager $transformerManager,
    ) {
    }

    /** @todo add room entity blablabla */
    public function process(Message $message): void
    {
        if (!$this->transformerManager->supports($message)) {
            throw new \RuntimeException('No transformer found for message.');
        }

        if (null === $connectionId = $message->metadata['connectionId'] ?? null) {
            throw new \RuntimeException('No connection id found in message metadata.');
        }

        if (!$this->connectionManager->hasConnection($message->metadata['connectionId'])) {
            $connection = new Connection('172.17.0.1', 0);
        }

        $connection ??= $this->connectionManager->getConnection($connectionId);

        $content = $this->transformerManager->transform($message);
        $this->transport->send($connection, json_encode($content), 'message');
    }
}
