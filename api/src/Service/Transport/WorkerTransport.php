<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\Connection;
use Symfony\Component\HttpClient\Messenger\PingWebhookMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class WorkerTransport implements GameTransportInterface
{
    public function __construct(
        private MessageBusInterface $bus,
        private string $workerUrl,
    ) {
    }

    public function send(Connection $connection, string $message): void
    {
        $payload = [

        ];
        $this->bus->dispatch(new PingWebhookMessage(
            'POST',
            $this->workerUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $message,
            ],
            true,
        ));
    }
}
