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

        if (!$room = $this->roomRepository->findOneBy(['owner' => $user])) {
            $room = new Room($user);
            $config = new RoomConfig(
                $room,
                $command->transport,
                GameEnum::from($command->game),
                $command->config,
                $command->commands,
            );

            $this->roomRepository->save($room, false);
            $this->roomConfigRepository->save($config);
        }

        return new JsonResponse([
            'id' => $room->getId(),
        ]);
    }
}
