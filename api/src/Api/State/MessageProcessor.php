<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Domain\Model\Message;
use App\Service\DaninTchat;
use App\Service\Factory\MessageFactory;
use App\Service\Message\MessageProcessor as ServiceMessageProcessor;
use App\Util\KillSwitch;

/**
 * @implements ProcessorInterface<Message, Message>
 */
final class MessageProcessor implements ProcessorInterface
{
    public function __construct(
        private ServiceMessageProcessor $messageProcessor,
        private MessageFactory $messageFactory,
        private DaninTchat $tchat,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        dd(KillSwitch::isEnabled('tchat'));
        if (!KillSwitch::isEnabled('tchat')) {
            return;
        }

        $message = $this->messageFactory->create($data->content);

        // @todo fetch room
        $this->messageProcessor->process($message);
        $this->tchat->sendMessage($uriVariables['id'], $message);

        return $data;
    }
}
