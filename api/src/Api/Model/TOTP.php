<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Domain\Command\Security\CheckTOTPCommand;

#[ApiResource(operations: [
    new Post(
        uriTemplate: '/check-totp',
        messenger: 'input',
        condition: "is_enable('totp')",
        input: CheckTOTPCommand::class,
    ),
])]
final class TOTP
{
}
