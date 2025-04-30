<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Model\Connection;
use App\Service\Exception\UnknowConnectionException;
use App\Service\Transport\GameTransportInterface;
use Psr\Log\LoggerInterface;

final class ConnectionManager
{
    /** @var array<string, Connection> */
    private array $connections = [];
    /** @var arraystring, string> */
    private array $tokens = [];

    public function __construct(
        private LoggerInterface $logger,
        private GameTransportInterface $gameTransport,
    ) {
    }

    public function addConnection(string $id, Connection $connection): void
    {
        $this->connections[$id] = $connection;
    }

    /**
     * @return string the connection Id
     */
    public function connect(string $host, int $port, ?string $id = null): string
    {
        $this->logger->info('Creating server {host}:{port}', [
            'host' => $host,
            'port' => $port,
        ]);

        $connection = new Connection($host, $port);
        $id ??= $this->getCurrentId();
        $this->addConnection($id, $connection);
        $this->handshake($id);

        return $id;
    }

    public function closeAllConnections(): void
    {
        foreach ($this->connections as $connection) {
            $connection->close();
        }
    }

    public function getConnection(string $id): ?Connection
    {
        if (null === $connection = ($this->connections[$id] ?? null)) {
            throw new UnknowConnectionException($id);
        }

        return $connection;
    }

    public function send(string $id, string|array $payload, string $type): void
    {
        $connection = $this->getConnection($id);

        $this->logger->info('Sending message to server', [
            'server' => $connection,
            'payload' => $payload,
            'type' => $type,
        ]);

        $this->gameTransport->send($connection, $this->convertPayload((array) $payload), $type);
    }

    private function handshake(string $id): void
    {
        $connection = $this->getConnection($id);
        $this->logger->info('Handshake with server', [
            'server' => $connection,
        ]);

        $this->generateToken($id);

        $this->gameTransport->send($connection, $this->convertPayload([
            'token' => $this->tokens[$id],
        ]), 'handshake');
    }

    private function convertPayload(array $payload): string
    {
        return json_encode($payload);
    }

    private function generateToken(string $serverId): string
    {
        $token = bin2hex(random_bytes(16));

        return $this->tokens[$serverId] = $token;
    }

    private function getCurrentId(): string
    {
        return (string) \count($this->connections);
    }
}
