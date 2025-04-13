<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class Connection
{
    private \Socket $socket;
    private bool $isConnected = false;

    public function __construct(
        public string $host,
        public int $port,
    ) {
    }

    public function connect(): void
    {
        if ($this->isConnected) {
            return;
        }

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (false === $this->socket) {
            throw new \RuntimeException('Failed to create socket: '.socket_strerror(socket_last_error()));
        }

        $result = socket_connect($this->socket, $this->host, $this->port);
        if (false === $result) {
            throw new \RuntimeException('Failed to connect: '.socket_strerror(socket_last_error($this->socket)));
        }
    }

    public function send(string $data): void
    {
        $bytesSent = socket_send($this->socket, $data, strlen($data), 0);

        if (false === $bytesSent) {
            throw new \RuntimeException('Failed to send data: '.socket_strerror(socket_last_error($this->socket)));
        }
    }
}
