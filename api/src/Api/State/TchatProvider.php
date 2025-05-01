<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Model\Message;
use App\Service\DaninTchat;

/**
 * @implements ProviderInterface<Message[]>
 */
final class TchatProvider implements ProviderInterface
{
    public function __construct(
        private DaninTchat $tchat,
    ) {
    }

    /**
     * @return Message[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return $this->tchat->getMessages($uriVariables['id']);
    }
}
