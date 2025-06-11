<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;
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

    public function transform(Message $message): array
    {
        return $this->getTransformerForMessage($message)->transform($message);
    }

    public function supports(Message $message): bool
    {
        return null !== $this->getTransformerForMessage($message);
    }

    public function getCommandsFromGame(string $game): array
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->getGameName() === $game) {
                return array_keys($transformer->getCommands());
            }
        }

        return [];
    }

    private function getTransformerForMessage(Message $message): ?MessageTransformerInterface
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($message)) {
                return $transformer;
            }
        }

        return null;
    }
}
