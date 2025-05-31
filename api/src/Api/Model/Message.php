<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Api\State\MessageProcessor;
use App\Api\State\TchatProvider;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/rooms/{id}/messages',
            processor: MessageProcessor::class,
            condition: 'is_enable("tchat")',
            status: 201,
        ),
        new GetCollection(
            uriTemplate: '/rooms/{id}/messages',
            condition: 'is_enable("tchat")',
            provider: TchatProvider::class,
        ),
    ],
)]
final class Message
{
    public function __construct(
        public string $content,
    ) {
    }
}
