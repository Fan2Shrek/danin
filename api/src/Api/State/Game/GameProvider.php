<?php

declare(strict_types=1);

namespace App\Api\State\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\GameRepository;

final class GameProvider implements ProviderInterface
{
    public function __construct(private GameRepository $gameRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $games = $this->gameRepository->findAll();
        if (empty($games)) {
            return [];
        }

        return array_map(function ($game) {
            return [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'description' => $game->getDescription(),
            ];
        }, $games);
    }
}
