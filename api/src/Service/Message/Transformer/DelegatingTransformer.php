<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;

final class DelegatingTransformer implements MessageTransformerInterface
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
