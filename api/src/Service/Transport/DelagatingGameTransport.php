<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Entity\RoomConfig;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class DelagatingGameTransport implements GameTransportInterface, ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    public function send(RoomConfig $roomConfig, string $data, string $type): void
    {
        (match ($roomConfig->getTransport()) {
            'socket' => $this->container->get('socket'),
            'mercure' => $this->container->get('mercure'),
            default => throw new \InvalidArgumentException('Unknown transport type: ' . $roomConfig->getTransportSettings()['type']),
        })->send($roomConfig, $data, $type);
    }

    public static function getSubscribedServices(): array
    {
        return [
            'socket' => SocketTransport::class,
            'mercure' => MercureTransport::class,
        ];
    }
}
