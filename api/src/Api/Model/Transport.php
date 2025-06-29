<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\TransportProvider;

#[ApiResource(operations: [
    new GetCollection(
        provider: TransportProvider::class,
    ),
])]
final readonly class Transport
{
    /**
     * @param string[] $fields
     */
    public function __construct(
        public string $id,
        public string $name,
        public array $fields = [],
    ) {
    }
}
