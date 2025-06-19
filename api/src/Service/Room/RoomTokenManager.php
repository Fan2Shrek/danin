<?php

declare(strict_types=1);

namespace App\Service\Room;

use App\Entity\Room;
use App\Entity\RoomToken;
use App\Repository\RoomRepository;
use App\Repository\RoomTokenRepository;

final class RoomTokenManager
{
    public function __construct(
        private RoomTokenRepository $roomTokenRepository,
        private RoomRepository $roomRepository,
    ) {
    }

    public function createForRoom(Room $room): RoomToken
    {
        $token = bin2hex(random_bytes(16));
        $roomToken = new RoomToken($token, (string) $room->getId());

        $this->roomTokenRepository->save($roomToken);

        return $roomToken;
    }

    public function useToken(string $id): Room
    {
        $token = $this->roomTokenRepository->find($id);

        if (!$token) {
            throw new \InvalidArgumentException(\sprintf('Invalid room token (%s)', $id));
        }

        $room = $this->roomRepository->find($token->getRoomId());

        $this->roomTokenRepository->delete($token);

        if (!$room) {
            throw new \InvalidArgumentException('Room not found for the given token');
        }

        return $room;
    }
}
