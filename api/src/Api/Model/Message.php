<?php

declare(strict_types=1);

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Api\State\MessageProcessor;

/** todo add tchat*/
#[ApiResource(
    operations: [
        new Post(
            processor: MessageProcessor::class,
            status: 201,
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
