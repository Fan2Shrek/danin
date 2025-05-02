<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Transport;

use App\Domain\Model\Connection;
use App\Service\Transport\WorkerTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WorkerTransportTest extends TestCase
{
    public function testSendMessageWithNewline(): void
    {
        $expectedContent = [
            'type' => 'test',
            'connection' => 'host',
            'content' => 'Hello World!',
        ];
        $connection = new Connection('host', 0);
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
        $gameTransport->send($connection, 'Hello World!', 'test');
    }
}
