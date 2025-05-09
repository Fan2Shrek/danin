<?php

declare(strict_types=1);

namespace App\Api\State\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\Message\Transformer\TransformerManager;

final class GameCommandProvider implements ProviderInterface
{
    public function __construct(
        private TransformerManager $messageTransformer,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->messageTransformer->getCommandsFromGame($uriVariables['id']);
    }
}
