<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Domain\Model\Message;
use App\Service\MessageProcessor as ServiceMessageProcessor;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<Message, Message>
 */
final class MessageProcessor implements ProcessorInterface
{
    public function __construct(
        private ServiceMessageProcessor $messageProcessor,
        private Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->messageProcessor->process(new Message($data->content, $this->security->getUser()));

        return $data;
    }
}
