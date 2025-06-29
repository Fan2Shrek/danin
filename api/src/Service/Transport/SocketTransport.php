<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Domain\Model\SocketConnection;
use App\Entity\RoomConfig;
use App\Service\Exception\UnknowConnectionException;
use Psr\Log\LoggerInterface;

class SocketTransport implements GameTransportInterface, ParametrableGameTransportInterface
{
    /** @var array<string, SocketConnection> */
    private array $connections = [];
    /** @var arraystring, string> */
    private array $tokens = [];

    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function send(RoomConfig $roomConfig, string $message, string $type): void
    {
        $connectionId = (string) $roomConfig->getId();
        if (!isset($this->connections[$connectionId])) {
            $this->addConnection($connectionId, $connection = $this->createConnection(
                $roomConfig->getTransportSettings()['host'],
                (int) $roomConfig->getTransportSettings()['port'],
            ));
            $connection->connect();

            $this->handshake($connectionId);
        }

        $this->doSend($connectionId, $message, $type);
    }

    public function getTransportSettings(): array
    {
        return [
            'host',
            'port',
        ];
    }

    protected function createConnection(string $host, int $port): SocketConnection
    {
        return new SocketConnection($host, $port);
    }

    private function doSend(string $connectionId, string $message, string $type): void
    {
        $this->logger->debug('Sending "{message}" to connection with id "{id}"', [
            'connection' => $connectionId,
            'message' => $message,
            'type' => $type,
        ]);
        $message = $this->convertPayload(array_merge(
            json_decode($message, true, 512, JSON_THROW_ON_ERROR),
            ['id' => $connectionId]
        ));

        if (!str_ends_with($message, "\n")) {
            $message .= "\n";
        }

        $this->getConnection($connectionId)->send($message);
    }

    private function addConnection(string $id, SocketConnection $connection): void
    {
        $this->connections[$id] = $connection;
    }

    private function handshake(string $id): void
    {
        $connection = $this->getConnection($id);
        $this->logger->info('Handshake with server', [
            'server' => $connection,
        ]);

        $this->generateToken($id);

        $this->doSend($id, $this->convertPayload([
            'token' => $this->tokens[$id],
        ]), 'handshake');
    }

    private function generateToken(string $serverId): string
    {
        $token = bin2hex(random_bytes(16));

        return $this->tokens[$serverId] = $token;
    }

    private function getConnection(string $id): SocketConnection
    {
        if (!isset($this->connections[$id])) {
            throw new \RuntimeException(\sprintf('No connection found for id "%s"', $id));
        }

        return $this->connections[$id];
    }

    private function getToken(string $serverId): string
    {
        if (null === $token = ($this->tokens[$serverId] ?? null)) {
            throw new UnknowConnectionException($serverId);
        }

        return $token;
    }

    private function convertPayload(array $payload): string
    {
        if (isset($payload['id'])) {
            $payload['token'] = $this->getToken($payload['id']);
            unset($payload['id']);
        }

        return json_encode($payload);
    }
}
