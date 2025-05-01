<?php

declare(strict_types=1);

namespace App\Service\Factory;

use App\Domain\Model\Message;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class MessageFactory
{
    public function __construct(
        private ClockInterface $clock,
        private Security $security,
    ) {
    }

    public function create(string $content, ?UserInterface $author = null): Message
    {
        $author ??= $this->security->getUser();

        return new Message(
            $content,
            $author instanceof User ? $author->getUsername() : $author->getUserIdentifier(),
            $this->clock->now(),
        );
    }
}
