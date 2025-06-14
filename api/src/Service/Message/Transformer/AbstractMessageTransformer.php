<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;
use App\Entity\RoomConfig;
use App\Service\Exception\UnknownCommandException;

abstract class AbstractMessageTransformer implements MessageTransformerInterface
{
    protected const string COMMAND_PREFIX = '!';

    protected bool $isInitialized = false;

    public function __construct(
        protected string $baseResourcePath,
    ) {
    }

    public function supports(RoomConfig $roomConfig): bool
    {
        return $this->getGame() === $roomConfig->getGame();
    }

    public function transform(Message $message, RoomConfig $roomConfig): array
    {
        if (!str_starts_with($message->content, self::COMMAND_PREFIX)) {
            return [];
        }

        $explodedCommand = explode(' ', $message->content, 2);

        $commandName = str_replace(self::COMMAND_PREFIX, '', $explodedCommand[0]);
        $args = $explodedCommand[1] ?? null;

        if (!$this->hasCommand($commandName) || !\in_array($commandName, $roomConfig->getCommands(), true)) {
            throw new UnknownCommandException(\sprintf('Unknown command "%s" for game "%s" (options are %s).', $commandName, $this->getGame()->value, implode(', ', $roomConfig->getCommands())));
        }

        if (!$this->isInitialized) {
            $this->initialize();
        }

        return $this->getCommands()[$commandName]($args);
    }

    protected function getResourcesPath(): string
    {
        return $this->baseResourcePath.$this->getGame()->value.DIRECTORY_SEPARATOR;
    }

    /**
     * The key is the command name
     * The value is the command callable.
     *
     * @return array<string, callable>
     */
    public function getCommands(): array
    {
        return [];
    }

    protected function initialize(): void
    {
        $this->initializeData();

        $this->isInitialized = true;
    }

    protected function initializeData(): void
    {
    }

    private function hasCommand(string $name): bool
    {
        return isset($this->getCommands()[$name]);
    }
}
