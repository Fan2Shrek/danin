<?php

declare(strict_types=1);

namespace App\Domain\Model;

/** @todo maybe replace by an interface */
readonly class Message
{
    public function __construct(
        public string $content,
        public string $author,
        public ?\DateTimeImmutable $sendAt = null,
        public array $metadata = [],
    ) {
    }
}
