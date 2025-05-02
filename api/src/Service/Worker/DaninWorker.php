<?php

declare(strict_types=1);

namespace App\Service\Worker;

use App\Service\ConnectionManager;
use Psr\Log\LoggerInterface;

final class DaninWorker
{
    public function __construct(
        private LoggerInterface $logger,
        private ConnectionManager $connectionManager,
    ) {
    }

    public function start(): void
    {
        $this->logger->info('Starting up Danin worker');
    }

    public function shutdown(): void
    {
        $this->logger->info('Shutting down Danin worker');
        $this->connectionManager->closeAllConnections();
    }

    public function processAction(WorkerAction $action): void
    {
        $this->logger->info('Processing {action}', [
            'action' => $action,
        ]);
        if ('create' === $action->type) {
            // Maybe add id?
            $this->connectionManager->connect(
                $action->data['host'],
                $action->data['port'],
                $action->data['host'],
            );

            return;
        }

        if (null === $id = $action->data['id'] ?? null) {
            throw new \RuntimeException('No id provided');
        }

        $this->connectionManager->send($id, $action->data, $action->type);
    }
}
