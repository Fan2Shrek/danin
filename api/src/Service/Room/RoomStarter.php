<?php

declare(strict_types=1);

namespace App\Service\Room;

use App\Entity\Room;
use App\Repository\RoomConfigRepository;
use App\Service\Transport\GameTransportInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Mercure\HubInterface;

final class RoomStarter
{
    public function __construct(
        private RoomConfigRepository $roomConfigRepository,
        private GameTransportInterface $transport,
        #[Target('mercure.hub.game')]
        private HubInterface $mercureHub,
        private RoomTokenManager $roomTokenManager,
    ) {
    }

    public function startRoom(Room $room): array
    {
        $config = $this->roomConfigRepository->findOneBy(['room' => $room]);
        $infos = [];

        if ('mercure' === $config->getTransport()) {
            $infos = [
                'local_setup' => true,
                'data' => [
                    'mercure-url' => $this->mercureHub->getPublicUrl().'?topic='.$room->getId(),
                    'mercure-token' => $this->mercureHub->getFactory()?->create([$room->getId()]) ?? '',
                ],
            ];
        } elseif ('socket' === $config->getTransport()) {
            $this->transport->send(
                $config,
                json_encode([
                    'host' => $config->getTransportSettings()['host'] ?? throw new \RuntimeException('Host is required for socket transport'),
                    'port' => $config->getTransportSettings()['port'] ?? throw new \RuntimeException('Port is required for socket transport'),
                ]),
                'create'
            );

            $infos = ['ok' => true];
        }

        $infos['providersToken'] = array_reduce(
            $config->getProviders(),
            function (array $carry, string $provider) use ($room) {
                $carry[$provider] = $this->roomTokenManager->createForRoom($room)->getId();

                return $carry;
            },
            []
        );

        return $infos;
    }
}
