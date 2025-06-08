<?php

declare(strict_types=1);

namespace App\Service\Worker;

use App\Service\Transport\GameTransportInterface;
use Psr\Log\LoggerInterface;

final class DaninWorker
{
    public function __construct(
        private LoggerInterface $logger,
        private GameTransportInterface $gameTransport,
    ) {
    }

    public function start(): void
    {
        $this->logger->info('Starting up Danin worker');
    }

    public function shutdown(): void
    {
        $this->logger->info('Shutting down Danin worker');
    }

    public function processAction(WorkerAction $action): void
    {
        $this->logger->info('Processing {action}', [
            'action' => $action,
        ]);

        $this->gameTransport->send(
            $action->data['roomConfig'],
            json_encode($action->data),
            $action->type,
        );
    }
}
