<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

final class IsaacMessageTransformer extends AbstractMessageTransformer
{
    private array $entityMapping = [];

    protected function getGameName(): string
    {
        return 'tboi';
    }

    protected function spawn(string $data): array
    {
        if (null === $id = $this->entityMapping[$data] ?? null) {
            throw new \RuntimeException(\sprintf('Unknown entity "%s".', $data));
        }

        return [
            'type' => 'spawn',
            'content' => $id,
        ];
    }

    protected function initializeData(): void
    {
        $this->entityMapping = require $this->getResourcesPath().'entities.php';
    }

    protected function getCommand(): array
    {
        return [
            'spawn' => $this->spawn(...),
            'bomb' => fn () => $this->spawn('bomb'),
        ];
    }
}
