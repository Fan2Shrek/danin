<?php

declare(strict_types=1);

namespace App\Service\Message;

use App\Domain\Model\Connection;
use App\Domain\Model\Message;
use App\Service\ConnectionManager;
use App\Service\Message\Transformer\MessageTransformerInterface;
use App\Service\Transport\GameTransportInterface;

final class MessageProcessor
{
    public function __construct(
        private ConnectionManager $connectionManager,
        private GameTransportInterface $transport,
        private MessageTransformerInterface $messageTransformer,
    ) {
    }

    public function process(Message $message): void
    {
        if (!$this->messageTransformer->supports($message)) {
            throw new \RuntimeException('No transformer found for message.');
        }

        // dev change to real handler
        $content = $this->messageTransformer->transform($message);
        $this->transport->send(new Connection('172.17.0.1', 0), json_encode($content), 'message');
    }
}
