<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\GameProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/games',
            provider: GameProvider::class,
        ),
    ],
)]
final class Game
{
}
