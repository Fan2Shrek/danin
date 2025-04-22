<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Transport;

use App\Service\Transport\GameTransport;
use App\Domain\Model\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GameTransportTest extends TestCase
{
    public function testSendMessageWithNewline(): void
    {
        $message = "Hello, World!";
        $expectedMessage = $message."\n";
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('send')
            ->with($expectedMessage);

        $gameTransport = new GameTransport($this->getLogger());
        $gameTransport->send($connection, $message, 'test');
    }

    public function testSendConnectionStartup(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('connect')
        ;

        $gameTransport = new GameTransport($this->getLogger());
        $gameTransport->send($connection, '', 'test');
    }

    private function getLogger(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }
}
