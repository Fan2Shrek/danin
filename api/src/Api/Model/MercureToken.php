<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Api\State\MercureTokenProcessor;

#[ApiResource(operations: [
    new Post(
        uriTemplate: '/mercure',
        processor: MercureTokenProcessor::class
    ),
])]
final readonly class MercureToken
{
    public function __construct(
        public array $topics = [],
    ) {
    }
}
