<?php

declare(strict_types=1);

namespace App\Service\Message;

use App\Domain\Model\Message;
use App\Entity\Room;
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

    public function process(Message $message, Room $room): void
    {
        if (!$this->transformerManager->supports($room->getRoomConfig())) {
            throw new \RuntimeException('No transformer found for message.');
        }

        $content = $this->transformerManager->transform($message, $room->getRoomConfig());
        $this->transport->send($room->getRoomConfig(), json_encode($content), 'message');
    }
}
