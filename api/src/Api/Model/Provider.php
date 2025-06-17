<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\ProviderProvider;

#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/providers',
        provider: ProviderProvider::class,
    ),
])]
final readonly class Provider
{
    public function __construct(
        public string $name,
    ) {
    }
}
