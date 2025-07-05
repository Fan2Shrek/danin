<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Enum\RoomStatusEnum;
use App\Repository\RoomRepository;

final class CurrentRoomsProvider implements ProviderInterface
{
    public function __construct(
        private RoomRepository $roomRepository,
    ) {
    }

    /**
     * @return Room[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return $this->roomRepository->findBy(['status' => RoomStatusEnum::STARTED], [], 10);
    }
}
