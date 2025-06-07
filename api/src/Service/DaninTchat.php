<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Model\Message;
use App\Service\Redis\RedisConnectionManager;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class DaninTchat
{
    public function __construct(
        private RedisConnectionManager $redisConnectionManager,
        private ClockInterface $clock,
        private HubInterface $hub,
    ) {
    }

    public function sendMessage(string $roomId, Message $message): void
    {
        $this->hub->publish(
            new Update(
                'aaa',
                /* $this->getKey($roomId), */
                json_encode([
                    'content' => $message->content,
                    'author' => $message->author,
                    'sendAt' => $message->sendAt->format('Y-m-d H:i:s'),
                ]),
            )
        );
        $this->addToList($roomId, $message);
    }

    public function getMessages(string $roomId): array
    {
        $messages = $this->redisConnectionManager->lrange($this->getKey($roomId), 0, 99);

        return array_map(
            static fn (string $message) => unserialize($message),
            $messages,
        );
    }

    private function addToList(string $roomId, Message $message): void
    {
        $this->redisConnectionManager->lpush(
            $this->getKey($roomId),
            serialize($message),
        );
        $this->redisConnectionManager->ltrim($this->getKey($roomId), 0, 99);
    }

    private function getKey(string $roomId): string
    {
        return \sprintf('danin_tchat/%s', $roomId);
    }
}
