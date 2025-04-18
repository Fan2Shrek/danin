<?php

declare(strict_types=1);

namespace App\Domain\Command;

class SendMessageCommand
{
    public function __construct(
        public string $content,
    ) {
    }
}
