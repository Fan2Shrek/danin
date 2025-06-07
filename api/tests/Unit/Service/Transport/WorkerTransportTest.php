<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Transport;

use App\Entity\Room;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;
use App\Service\Transport\WorkerTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WorkerTransportTest extends TestCase
{
    public function testSendMessage(): void
    {
        $expectedContent = [
            'type' => 'test',
            'connection' => '',
            'content' => 'Hello World!',
        ];
        $ed = $this->createMock(EventDispatcherInterface::class);
        $ed->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function ($event) use ($expectedContent) {
                    self::assertEquals($expectedContent, $event->data);

                    return true;
                }),
                'tchat_message'
            );

        $gameTransport = new WorkerTransport($ed, $this->createMock(\Psr\Log\LoggerInterface::class));
        $gameTransport->send($this->getConfigRoom(), 'Hello World!', 'test');
    }

    private function getConfigRoom(): RoomConfig
    {
        return new class(
            $this->createMock(Room::class),
            'socket',
            GameEnum::THE_BINDING_OF_ISAAC,
        ) extends RoomConfig {
            public function getId(): int
            {
                return 1;
            }
        };
    }
}
