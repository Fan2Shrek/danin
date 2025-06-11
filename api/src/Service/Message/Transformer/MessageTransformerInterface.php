<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;

interface MessageTransformerInterface
{
    public function transform(Message $message, RoomConfig $roomConfig): array;

    public function supports(RoomConfig $roomConfig): bool;

    public function getGame(): GameEnum;

    /**
     * The key is the command name
     * The value is the command callable.
     *
     * @return array<string, callable>
     */
    public function getCommands(): array;
}
