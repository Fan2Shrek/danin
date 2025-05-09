<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;

interface MessageTransformerInterface
{
    public function transform(Message $message): array;

    public function supports(Message $message): bool;

    public function getGameName(): string;

    /**
     * The key is the command name
     * The value is the command callable.
     *
     * @return array<string, callable>
     */
    public function getCommands(): array;
}
