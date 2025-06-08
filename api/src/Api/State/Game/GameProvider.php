<?php

declare(strict_types=1);

namespace App\Api\State\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\GameRepository;

final class GameProvider implements ProviderInterface
{
    public function __construct(
        private GameRepository $gameRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $games = $this->gameRepository->findAll();
        if (empty($games)) {
            return [];
        }

        // Temp to test template
        $gamesList = $games;
        for ($i = 1; $i < 10; $i++) {
            $gamesList = array_merge($gamesList, $games);
        }

        return $gamesList;
    }
}
