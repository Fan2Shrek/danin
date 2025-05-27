<?php

declare(strict_types=1);

namespace App\Api\State\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CommandRepository;
use App\Service\Message\Transformer\TransformerManager;
use Doctrine\ORM\EntityManagerInterface;

final class GameCommandProvider implements ProviderInterface
{
    public function __construct(
        private TransformerManager $messageTransformer,
        private CommandRepository $commandRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $commands = [];

        foreach ($this->messageTransformer->getCommandsFromGame($uriVariables['id']) as $command) {
            // @todo: Optimize this to avoid too many queries
            $commandEntity = $this->commandRepository->find($uriVariables['id'].'_'.$command);

            if (null === $commandEntity) {
                continue;
            }

            $commands[] = [
                'id' => $command,
                'description' => $commandEntity->getDescription(),
            ];
        }

        return $commands;
    }
}
