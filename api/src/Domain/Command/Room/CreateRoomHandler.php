<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

use App\Entity\Room;
use App\Entity\User;
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
            $this->roomRepository->save($room);
        }

        return new JsonResponse([
            'id' => $room->getId(),
        ]);
    }
}
