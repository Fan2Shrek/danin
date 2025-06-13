<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;

final class TransformerManager
{
    /**
     * @param iterable<MessageTransformerInterface> $transformers
     */
    public function __construct(
        private iterable $transformers,
    ) {
    }

    public function transform(Message $message, RoomConfig $roomConfig): array
    {
        return $this->getTransformerForConfig($roomConfig)->transform($message, $roomConfig);
    }

    public function supports(RoomConfig $roomConfig): bool
    {
        return null !== $this->getTransformerForConfig($roomConfig);
    }

    public function getCommandsFromGame(GameEnum $game): array
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->getGame() === $game) {
                return array_keys($transformer->getCommands());
            }
        }

        return [];
    }

    private function getTransformerForConfig(RoomConfig $config): ?MessageTransformerInterface
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($config)) {
                return $transformer;
            }
        }

        return null;
    }
}
