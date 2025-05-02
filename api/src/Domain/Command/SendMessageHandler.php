<?php

declare(strict_types=1);

namespace App\Domain\Command;

use App\Api\Model\Message;
use App\Service\Message\MessageProcessor;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendMessageHandler
{
    public function __construct(
        private Security $security,
        private MessageProcessor $messageProcessor,
    ) {
    }

    public function __invoke(SendMessageCommand $command): void
    {
        $msg = new Message($command->content, $this->security->getUser());

        $this->messageProcessor->process($msg);
    }
}
