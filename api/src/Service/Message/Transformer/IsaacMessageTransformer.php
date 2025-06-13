<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Enum\GameEnum;

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
    private array $soundMapping = [];

    public function getGame(): GameEnum
    {
        return GameEnum::THE_BINDING_OF_ISAAC;
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

    protected function play(string $data): array
    {
        if (null === $id = $this->soundMapping[$data] ?? null) {
            throw new \RuntimeException(\sprintf('Unknown sound "%s".', $data));
        }

        return [
            'type' => 'sound',
            'content' => $id,
        ];
    }

    protected function initializeData(): void
    {
        $this->entityMapping = require $this->getResourcesPath().'entities.php';
        $this->activeItemMapping = require $this->getResourcesPath().'active_items.php';
        $this->soundMapping = require $this->getResourcesPath().'sounds.php';
    }

    public function getCommands(): array
    {
        return [
            'spawn' => $this->spawn(...),
            'bomb' => fn () => $this->spawn('bomb'),
            'use' => $this->use(...),
            'play' => $this->play(...),
        ];
    }
}
