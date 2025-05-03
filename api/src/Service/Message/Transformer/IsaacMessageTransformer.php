<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

/**
 * This class is highly link to the isaac danin mod.
 * See mods/TBOI/resources/handlers.lua.
 *
 * @author Pierre Ambroise<pierre27.ambroise@gmail.com>
 */
final class IsaacMessageTransformer extends AbstractMessageTransformer
{
    private array $entityMapping = [];
    private array $activeItemMapping = [];

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

    protected function use(?string $activeItemName): array
    {
        if (null === $activeItemName) {
            return [
                'type' => 'activate',
                'content' => null,
            ];
        }

        if (null === $id = $this->activeItemMapping[$activeItemName] ?? null) {
            throw new \RuntimeException(\sprintf('Unknown active item "%s".', $activeItemName));
        }

        return [
            'type' => 'activate',
            'content' => $id,
        ];
    }

    protected function initializeData(): void
    {
        $this->entityMapping = require $this->getResourcesPath().'entities.php';
        $this->activeItemMapping = require $this->getResourcesPath().'active_items.php';
    }

    protected function getCommand(): array
    {
        return [
            'spawn' => $this->spawn(...),
            'bomb' => fn () => $this->spawn('bomb'),
            'use' => $this->use(...),
        ];
    }
}
