<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Transport;

use App\Domain\Model\SocketConnection;
use App\Entity\Room;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;
use App\Service\Transport\SocketTransport;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SocketTransportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SpySocketConnection::reset();
    }

    public function testSendConnectionStartup(): void
    {
        $gameTransport = $this->getSocketTransport();
        $gameTransport->send($this->getRoomConfig(), '{}', 'test');

        self::assertSame(1, SpySocketConnection::$calls['connect']);
    }

    public function testSendTokenOnFirstSend(): void
    {
        $gameTransport = $this->getSocketTransport();
        $gameTransport->send($this->getRoomConfig(), '{}', 'test');

        self::assertArrayHasKey('token', $this->decodeMessage(SpySocketConnection::$calls['send'][0]));
    }

    public function testTokenResend(): void
    {
        $gameTransport = $this->getSocketTransport();
        $gameTransport->send($this->getRoomConfig(), '{}', 'test');

        $token = $this->decodeMessage(SpySocketConnection::$calls['send'][0])['token'];
        self::assertSame($token, $this->decodeMessage(SpySocketConnection::$calls['send'][1])['token']);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }

    private function getSocketTransport(): SocketTransport
    {
        return new class($this->getLogger()) extends SocketTransport {
            protected function createConnection(string $host, int $port): SocketConnection
            {
                return new SpySocketConnection($host, $port);
            }
        };
    }

    private function getRoomConfig(string $host = 'localhost', int $port = 12345): RoomConfig
    {
        return new class($this->createMock(Room::class), 'socket', GameEnum::THE_BINDING_OF_ISAAC, ['host' => $host, 'port' => $port], []) extends RoomConfig {
            public function getId(): int
            {
                return 1;
            }
        };
    }

    private function decodeMessage(string $msg): array
    {
        return json_decode(str_replace("\”", '', $msg), true, 512, JSON_THROW_ON_ERROR);
    }
}

class SpySocketConnection extends SocketConnection
{
    public static array $calls = [
        'connect' => 0,
        'send' => [],
    ];

    public function connect(): void
    {
        ++static::$calls['connect'];
    }

    public function send(string $data): void
    {
        static::$calls['send'][] = $data;
    }

    public static function reset(): void
    {
        static::$calls = [];
    }
}
