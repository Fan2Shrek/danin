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
    private array $passiveItem = [];
    private array $soundMapping = [];

    public function getGame(): GameEnum
    {
        return GameEnum::THE_BINDING_OF_ISAAC;
    }

    protected function spawn(string $data): array
    {
        return [
            'type' => 'spawn',
            'content' => $this->resolveId($this->entityMapping, $data, 'entity'),
        ];
    }

    protected function spawnPedestal(string $data): array
    {
        return [
            'type' => 'pedestal',
            'content' => $this->resolveId($this->passiveItem, $data, 'passive item'),
        ];
    }

    protected function giveItem(string $data): array
    {
        return [
            'type' => 'give-item',
            'content' => $this->resolveId($this->passiveItem, $data, 'passive item'),
        ];
    }

    protected function use(?string $activeItemName): array
    {
        return [
            'type' => 'activate',
            'content' => null === $activeItemName ? null : $this->resolveId($this->activeItemMapping, $activeItemName, 'active item'),
        ];
    }

    protected function play(string $data): array
    {
        return [
            'type' => 'sound',
            'content' => $this->resolveId($this->soundMapping, $data, 'sound'),
        ];
    }

    private function resolveId(array $mapping, string $name, string $label): int
    {
        if (null === $id = $mapping[$name] ?? null) {
            throw new \RuntimeException(\sprintf('Unknown %s "%s".', $label, $name));
        }

        return $id;
    }

    protected function initializeData(): void
    {
        $this->entityMapping = require $this->getResourcesPath().'entities.php';
        $this->soundMapping = require $this->getResourcesPath().'sounds.php';

        $items = require $this->getResourcesPath().'items.php';
        $this->activeItemMapping = array_map(
            static fn (array $item): int => $item['id'],
            array_filter($items, static fn (array $item): bool => $item['is_active']),
        );
        $this->passiveItem = array_map(
            static fn (array $item): int => $item['id'],
            array_filter($items, static fn (array $item): bool => !$item['is_active']),
        );
    }

    public function getCommands(): array
    {
        return [
            'spawn' => $this->spawn(...),
            'bomb' => fn () => $this->spawn('bomb'),
            'use' => $this->use(...),
            'play' => $this->play(...),
			'pedestal' => $this->spawnPedestal(...),
			'give' => $this->giveItem(...),
        ];
    }
}
