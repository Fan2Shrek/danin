<?php

declare(strict_types=1);

namespace App\Service\Provider;

use App\Service\Bot\DiscordBot;

final class DiscordProvider implements ProviderInterface
{
    public function __construct(
        private DiscordBot $bot,
    ) {
    }

    public function getName(): string
    {
        return 'Discord';
    }

    public function start(): void
    {
        $this->bot->start();
    }
}
