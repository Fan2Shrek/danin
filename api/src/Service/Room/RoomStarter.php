<?php

declare(strict_types=1);

namespace App\Service\Room;

use App\Entity\Room;
use App\Repository\RoomConfigRepository;
use App\Service\Transport\GameTransportInterface;
use Symfony\Component\Mercure\HubInterface;

final class RoomStarter
{
    public function __construct(
        private RoomConfigRepository $roomConfigRepository,
        private GameTransportInterface $transport,
        private HubInterface $mercureHub,
    ) {
    }

    public function startRoom(Room $room): array
    {
        $config = $this->roomConfigRepository->findOneBy(['room' => $room]);

        if ('mercure' === $config->getTransport()) {
            return [
                'local_setup' => true,
                'data' => [
                    'mercure-url' => $this->mercureHub->getPublicUrl().'?topic='.$room->getId(),
                    'mercure-token' => $this->mercureHub->getFactory()?->create([$room->getId()]) ?? '',
                ],
            ];
        }

        // socket
        $this->transport->send(
            $config,
            json_encode([
                'host' => $config->getTransportSettings()['host'] ?? throw new \RuntimeException('Host is required for socket transport'),
                'port' => $config->getTransportSettings()['port'] ?? throw new \RuntimeException('Port is required for socket transport'),
            ]), 'create');

        return [
            'ok' => true,
        ];
    }
}
