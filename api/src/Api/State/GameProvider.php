<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Enum\GameEnum;

final class GameProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return array_map(
            fn (GameEnum $game) => [
                'id' => $game->value,
                'name' => $this->formatName($game->name),
            ],
            GameEnum::cases()
        );
    }

    private function formatName(string $name): string
    {
        return ucfirst(strtolower(str_replace('_', ' ', $name)));
    }
}
