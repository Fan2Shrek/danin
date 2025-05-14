<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Model\Message;
use App\Repository\RoomRepository;
use App\Service\DaninTchat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @implements ProviderInterface<Message[]>
 */
final class TchatProvider implements ProviderInterface
{
    public function __construct(
        private RoomRepository $roomRepository,
        private DaninTchat $tchat,
    ) {
    }

    /**
     * @return Message[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        if (!$room = $this->roomRepository->find($uriVariables['id'])) {
            throw HttpException::fromStatusCode(Response::HTTP_NOT_FOUND, 'Room not found');
        }

        return $this->tchat->getMessages((string) $room->getId());
    }
}
