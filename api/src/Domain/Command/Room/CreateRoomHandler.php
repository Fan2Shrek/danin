<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

use App\Entity\Room;
use App\Entity\RoomConfig;
use App\Entity\User;
use App\Enum\GameEnum;
use App\Repository\RoomConfigRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateRoomHandler
{
    public function __construct(
        private Security $security,
        private RoomRepository $roomRepository,
        private RoomConfigRepository $roomConfigRepository,
    ) {
    }

    public function __invoke(CreateRoomCommand $command): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        if ('socket' === $command->transport) {
            $this->assertSafeSocketTarget($command->config['host'] ?? null, $command->config['port'] ?? null);
        }

        if ($room = $this->roomRepository->findOneBy(['owner' => $user])) {
            $this->roomRepository->delete($room);
        }

        $room = new Room($user);
        $config = new RoomConfig(
            $room,
            $command->transport,
            GameEnum::from($command->game),
            $command->config,
            $command->commands,
            $command->providers,
        );

        $this->roomRepository->save($room, false);
        $this->roomConfigRepository->save($config);

        return new JsonResponse([
            'id' => $room->getId(),
        ]);
    }

    // shortcut: resolves host once, a DNS-rebind between this check and the actual connect could slip through; upgrade with an allow-list if socket targets become attacker-reachable in practice
    private function assertSafeSocketTarget(mixed $host, mixed $port): void
    {
        if (!\is_string($host) || '' === $host) {
            throw new \RuntimeException('Host is required for socket transport');
        }

        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new \RuntimeException(\sprintf('Host "%s" is not allowed for socket transport', $host));
        }

        if (!is_numeric($port) || (int) $port < 1 || (int) $port > 65535) {
            throw new \RuntimeException('Invalid port for socket transport');
        }
    }
}
