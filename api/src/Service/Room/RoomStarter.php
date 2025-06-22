<?php

declare(strict_types=1);

namespace App\Service\Room;

use App\Entity\Room;
use App\Repository\ProviderRepository;
use App\Repository\RoomConfigRepository;
use App\Service\Provider\ProviderManager;
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
        private ProviderManager $providerManager,
        private ProviderRepository $providerRepository,
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
                    'port' => (int) $config->getTransportSettings()['port'] ?? throw new \RuntimeException('Port is required for socket transport'),
                ]),
                'create'
            );

            $infos = ['ok' => true];
        }

        $infos['providers'] = array_reduce(
            $config->getProviders(),
            function (array $carry, string $providerName) use ($room) {
                if (!$this->providerManager->getProvider($providerName)) {
                    // todo this should be check on create call
                    throw new \RuntimeException(\sprintf('Provider "%s" not found.', $providerName));
                }
                $providerEntity = $this->providerRepository->find($providerName);

                $carry[$providerName] = [
                    'token' => $this->roomTokenManager->createForRoom($room)->getId(),
                    'command' => $providerEntity->getCommand(),
                ];

                return $carry;
            },
            []
        );

        return $infos;
    }
}
