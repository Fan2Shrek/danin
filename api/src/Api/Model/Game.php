<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\Game\GameCommandProvider;
use App\Api\State\Game\GameProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/games',
            provider: GameProvider::class,
        ),
        new GetCollection(
            uriTemplate: '/games/{id}/commands',
            provider: GameCommandProvider::class,
        ),
    ],
)]
final class Game
{
}
