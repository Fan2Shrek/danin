<?php

declare(strict_types=1);

namespace App\Service\Message;

use App\Domain\Model\Message;
use App\Repository\RoomConfigRepository;
use App\Service\Message\Transformer\TransformerManager;
use App\Service\Transport\GameTransportInterface;

final class MessageProcessor
{
    public function __construct(
        private GameTransportInterface $transport,
        private TransformerManager $transformerManager,

        private RoomConfigRepository $roomConfigRepository,
    ) {
    }

    /** @todo add room entity blablabla */
    public function process(Message $message): void
    {
        if (!$this->transformerManager->supports($message)) {
            throw new \RuntimeException('No transformer found for message.');
        }

        $content = $this->transformerManager->transform($message);
        $this->transport->send(current($this->roomConfigRepository->findAll()), json_encode($content), 'message');
    }
}
