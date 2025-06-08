<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

use App\Repository\RoomConfigRepository;
use App\Repository\RoomRepository;
use App\Service\Room\RoomStarter;
use App\Service\Transport\GameTransportInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class StartRoomHandler
{
    public function __construct(
        private Security $security,
        private GameTransportInterface $transport,
        private RoomConfigRepository $roomConfigRepository,
        private RoomRepository $roomRepository,
        private RoomStarter $roomStarter,
    ) {
    }

    public function __invoke(StartRoomCommand $command)
    {
        $room = $this->roomRepository->findOneBy(['owner' => $this->security->getUser()]);

        return new JsonResponse($this->roomStarter->startRoom($room));
    }
}
