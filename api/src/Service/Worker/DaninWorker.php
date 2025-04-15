<?php

declare(strict_types=1);

namespace App\Service\Worker;

use App\Domain\Model\Connection;
use App\Service\Transport\GameTransportInterface;
use Psr\Log\LoggerInterface;

final class DaninWorker
{
    /** @var arraystring, Connection> */
    private array $servers = [];
    private ?\Closure $eh = null;

    public function __construct(
        private ActionQueue $queue,
        private LoggerInterface $logger,
        private GameTransportInterface $gameTransport,
    ) {
    }

    public function setUp(): void
    {
        $this->eh = set_exception_handler($this->gracefulShutdown(...));
    }

    public function tearDown(): void
    {
        foreach ($this->servers as $server) {
            $server->close();
        }
    }

    public function consume(): never
    {
        while (1) {
            if (!$action = $this->queue->getNextAction()) {
                sleep(1);
                continue;
            }

            $this->processAction($action);
        }
    }

    private function processAction(WorkerAction $action): void
    {
        if ($action === null) {
            return;
        }

        $server = $this->getServer($action->serverId);
        if ($server === null) {
            return;
        }

        /** todo handly start/stop action */
        $this->gameTransport->send($server, $action->type, $action->data);
    }

    private function addServer(Connection $server): void
    {
        $this->servers[] = $server;
    }

    private function getServer(string $id): ?Connection
    {
        return $this->servers[$id] ?? null;
    }

    private function gracefulShutdown(\Throwable $e): void
    {
        $this->logger->info('An exception occurred: '.$e->getMessage());
        $this->logger->info('Gracefully shutting down DaninWorker...');

        foreach ($this->servers as $server) {
            $server->close();
        }

        $this->logger->info('DaninWorker shut down successfully.');

        if ($this->eh) {
            ($this->eh)($e);
        }
    }
}
