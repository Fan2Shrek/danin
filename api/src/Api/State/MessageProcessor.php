<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Model\Message as ApiMessage;
use App\Domain\Model\Message;
use App\Repository\RoomRepository;
use App\Service\DaninTchat;
use App\Service\Factory\MessageFactory;
use App\Service\Message\MessageProcessor as ServiceMessageProcessor;
use App\Util\KillSwitch;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @implements ProcessorInterface<Message, Message>
 */
final class MessageProcessor implements ProcessorInterface
{
    public function __construct(
        private ServiceMessageProcessor $messageProcessor,
        private RoomRepository $roomRepository,
        private MessageFactory $messageFactory,
        private DaninTchat $tchat,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?ApiMessage
    {
        if (!KillSwitch::isEnabled('tchat')) {
            return null;
        }

        if (!$room = $this->roomRepository->find($uriVariables['id'])) {
            throw HttpException::fromStatusCode(Response::HTTP_NOT_FOUND, 'Room not found');
        }

        $message = $this->messageFactory->create($data->content);

        $this->messageProcessor->process($message);
        $this->tchat->sendMessage((string) $room->getId(), $message);

        return $data;
    }
}
