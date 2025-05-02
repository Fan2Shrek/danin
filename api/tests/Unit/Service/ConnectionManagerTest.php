<?php

namespace App\Tests\Unit\Service;

use App\Domain\Model\Connection;
use App\Service\ConnectionManager;
use App\Service\Transport\GameTransportInterface;
use App\Tests\Resources\GameClientMock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ConnectionManagerTest extends TestCase
{
    public function testConnect(): void
    {
        $transport = $this->createMock(GameTransportInterface::class);
        $transport
            ->expects($this->once())
            ->method('send')
        ;
        $manager = $this->getConnectionManager(gameTransport: $transport);
        $manager->connect('localhost', 8080, 'testId');

        self::assertNotNull($manager->getConnection('testId'));
        self::assertNotNull($manager->getToken('testId'));
    }

    public function testCloseAllConnections(): void
    {
        $connection1 = $this->createMock(Connection::class);
        $connection1
            ->expects($this->once())
            ->method('close')
        ;
        $connection2 = $this->createMock(Connection::class);
        $connection2
            ->expects($this->once())
            ->method('close')
        ;

        $manager = $this->getConnectionManager();
        $manager->addConnection('testId1', $connection1);
        $manager->addConnection('testId2', $connection2);
        $manager->closeAllConnections();
    }

    public function testHandshake(): void
    {
        $mock = new GameClientMock();
        $manager = $this->getConnectionManager($mock);

        $connection = new Connection('localhost', 8080);
        $manager->addConnection('testId', $connection);
        $manager->handshake('testId');

        self::assertCount(1, $mock->getMessages());
        self::assertArrayHasKey('token', json_decode($mock->getMessages()[0]['message'], true));
        self::assertSame($connection, $mock->getMessages()[0]['connection']);
    }

    public function testTokenAfterHandshake(): void
    {
        $mock = new GameClientMock();
        $manager = $this->getConnectionManager($mock);

        $connection = new Connection('localhost', 8080);
        $manager->addConnection('testId', $connection);
        $manager->handshake('testId');
        $manager->send('testId', 'testMessage', 'testType');

        $token = $manager->getToken('testId');
        $content = json_decode($mock->getMessages()[1]['message'], true);

        self::assertCount(2, $mock->getMessages());
        self::assertArrayHasKey('token', $content);
        self::assertSame($connection, $mock->getMessages()[1]['connection']);
        self::assertSame($token, $content['token']);
    }

    public function testThrowExceptionOnUnknownConnection(): void
    {
        $this->expectException(\App\Service\Exception\UnknowConnectionException::class);
        $this->expectExceptionMessage('Unknown connection with id "unknownId"');

        $manager = $this->getConnectionManager();
        $manager->getConnection('unknownId');
    }

    private function getConnectionManager(
        ?GameTransportInterface $gameTransport = null,
    ): ConnectionManager {
        return new ConnectionManager(
            $this->createMock(LoggerInterface::class),
            $gameTransport ?? $this->createMock(GameTransportInterface::class)
        );
    }
}
