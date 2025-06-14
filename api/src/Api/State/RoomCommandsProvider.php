<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CommandRepository;
use App\Repository\RoomConfigRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<array>
 */
final class RoomCommandsProvider implements ProviderInterface
{
    public function __construct(
        private RoomConfigRepository $roomConfigRepository,
        private CommandRepository $commandRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $roomConfig = $this->roomConfigRepository->findOneBy(['room' => $uriVariables['id']]);

        if (!$roomConfig) {
            throw new NotFoundHttpException(\sprintf('Room with id "%s" not found.', $uriVariables['id']));
        }

        $commands = [];
        foreach ($roomConfig->getCommands() as $command) {
            $commands[] = $this->commandRepository->find("{$roomConfig->getGame()->value}_{$command}");
        }

        return $commands;
    }
}
