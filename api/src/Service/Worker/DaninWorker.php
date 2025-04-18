<?php

declare(strict_types=1);

namespace App\Service\Worker;

use App\Domain\Model\Connection;
use App\Service\Redis\RedisListenerManager;
use App\Service\Transport\GameTransportInterface;
use Psr\Log\LoggerInterface;

final class DaninWorker
{
    /** @var arraystring, Connection> */
    private array $servers = [];
    /** @var arraystring, string> */
    private array $tokens = [];
    /** @var callable */
    private $eh;

    public function __construct(
        private RedisListenerManager $redisListenerManager,
        private LoggerInterface $logger,
        private GameTransportInterface $gameTransport,
    ) {
    }

    public function consume(): void
    {
        $this->setUp();
    }

    public function setUp(): void
    {
        $this->eh = set_exception_handler($this->gracefulShutdown(...));

        // dev
        $this->servers['0'] = new Connection('0', '172.17.0.1', 12345);

        $this->redisListenerManager->startListening();
    }

    public function tearDown(): void
    {
        foreach ($this->servers as $server) {
            $server->close();
        }
    }

    public function processAction(WorkerAction $action): void
    {
        if ('create' === $action->type) {
            $this->create($action->data);

            return;
        }

        $id = $action->data['id'] ?? null;

        if (null === $id || null === $server = $this->getServer($id)) {
            return;
        }

        $this->gameTransport->send($server, $this->convertPayload($action->data), $action->type);
    }

    private function create(array $config): void
    {
        $server = new Connection(
            $config['id'],
            $config['host'],
            $config['port'],
        );

        $this->logger->info('Creating server', [
            'server' => $server,
        ]);

        $server->connect();

        $this->addServer($server);
        $this->handshake($server);
    }

    private function convertPayload(array $payload): string
    {
        if (isset($payload['id'])) {
            $payload['token'] = $this->tokens[$payload['id']] ?? throw new \RuntimeException('Token not found for server: '.$serverId);
            unset($payload['id']);
        }

        return json_encode($payload);
    }

    private function addServer(Connection $server): void
    {
        $this->servers[$server->id] = $server;
    }

    private function getServer(string $id): ?Connection
    {
        return $this->servers[$id] ?? null;
    }

    private function gracefulShutdown(\Throwable $e): void
    {
        $this->logger->info('An exception occurred: '.$e->getMessage());
        $this->logger->info('Gracefully shutting down DaninWorker...');

        $this->tearDown();

        $this->logger->info('DaninWorker shut down successfully.');

        if ($this->eh) {
            ($this->eh)($e);
        }
    }

    private function handshake(Connection $server): void
    {
        $this->logger->info('Handshake with server', [
            'server' => $server,
        ]);

        $this->tokens[$server->id] = $this->generateToken();

        $this->gameTransport->send($server, $this->convertPayload([
            'token' => $this->tokens[$server->id],
        ]), 'handshake');
    }

    private function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));

        return $token;
    }
}
