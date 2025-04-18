<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Domain\Command\Room\StartRoomCommand;

/** add id */
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/rooms/start',
            messenger: 'input',
            input: StartRoomCommand::class,
            output: false,
        ),
    ]
)]
class Room
{
}
