<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Transport;

use App\Entity\Room;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;
use App\Service\Transport\MercureTransport;
use App\Tests\Resources\HubSpy;
use PHPUnit\Framework\TestCase;

final class MercureTransportTest extends TestCase
{
    public function testSend(): void
    {
        $transport = new MercureTransport(
            new HubSpy(),
        );

        $roomConfig = new class (
            $this->createMock(Room::class),
            'socket',
            GameEnum::THE_BINDING_OF_ISAAC,
        ) extends RoomConfig {
            public function getId(): int
            {
                return 1;
            }
        };

        $transport->send($roomConfig, '{"message": "test"}', 'test');

        self::assertSame(
            '{"message": "test"}',
            HubSpy::$lastUpdate->getData(),
        );
    }
}
