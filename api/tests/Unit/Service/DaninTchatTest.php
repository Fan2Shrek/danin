<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Domain\Model\Message;
use App\Service\DaninTchat;
use App\Service\Redis\RedisConnectionManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class DaninTchatTest extends TestCase
{
    public function testSendMessageMercure(): void
    {
        $mercure = $this->createMock(HubInterface::class);
        $mercure
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->callback(function (Update $update) {
                    self::assertSame('danin_tchat/roomId', $update->getTopics()[0]);
                    self::assertSame(
                        [
                            'content' => 'content',
                            'author' => 'author',
                            'sendAt' => '2023-01-01 00:00:00',
                        ],
                        json_decode($update->getData(), true, 512, JSON_THROW_ON_ERROR)
                    );

                    return true;
                }),
            );
        $tchat = $this->getDaninTchat(
            hub: $mercure,
        );

        $tchat->sendMessage('roomId', new Message('content', 'author', new \DateTimeImmutable('2023-01-01 00:00:00')));
    }

    public function testSendMessageAddToList(): void
    {
        $redis = $this->createMock(RedisConnectionManager::class);
        $message = new Message('content', 'author', new \DateTimeImmutable('2023-01-01 00:00:00'));
        $redis
            ->expects($this->once())
            ->method('lpush')
            ->with(
                'danin_tchat/roomId',
                $this->callback(function (string $value) use ($message) {
                    self::assertSame(
                        serialize($message),
                        $value,
                    );

                    return true;
                }),
            );
        $redis
            ->expects($this->once())
            ->method('ltrim')
            ->with('danin_tchat/roomId', 0, 99);

        $tchat = $this->getDaninTchat(
            redisConnectionManager: $redis,
        );

        $tchat->sendMessage('roomId', $message);
    }

    public function testGetMessages(): void
    {
        $redis = $this->createMock(RedisConnectionManager::class);
        $redis
            ->expects($this->once())
            ->method('lrange')
            ->with('danin_tchat/roomId', 0, 99)
            ->willReturn([
                serialize(new Message('content', 'author', new \DateTimeImmutable('2023-01-01 00:00:00'))),
                serialize(new Message('content2', 'author2', new \DateTimeImmutable('2023-01-01 00:00:01'))),
            ]);

        $tchat = $this->getDaninTchat(
            redisConnectionManager: $redis,
        );

        $messages = $tchat->getMessages('roomId');

        self::assertCount(2, $messages);
        self::assertSame('content', $messages[0]->content);
        self::assertSame('author', $messages[0]->author);
        self::assertSame('2023-01-01 00:00:00', $messages[0]->sendAt->format('Y-m-d H:i:s'));
    }

    private function getDaninTchat(
        ?RedisConnectionManager $redisConnectionManager = null,
        ?ClockInterface $clock = null,
        ?HubInterface $hub = null,
    ): DaninTchat {
        return new DaninTchat(
            $redisConnectionManager ?? $this->createMock(RedisConnectionManager::class),
            $clock ?? $this->createMock(ClockInterface::class),
            $hub ?? $this->createMock(HubInterface::class),
        );
    }
}
