<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;
use App\Service\Exception\UnknownCommandException;

abstract class AbstractMessageTransformer implements MessageTransformerInterface
{
    protected const string COMMAND_PREFIX = '!';

    protected bool $isInitialized = false;

    public function __construct(
        protected string $baseResourcePath,
    ) {
    }

    public function supports(Message $message): bool
    {
        // return $this->getGameName() === $something;
        return true;
    }

    public function transform(Message $message): array
    {
        if (!str_starts_with($message->content, self::COMMAND_PREFIX)) {
            return [];
        }

        $explodedCommand = explode(' ', $message->content, 2);

        $commandName = str_replace(self::COMMAND_PREFIX, '', $explodedCommand[0]);
        $args = $explodedCommand[1] ?? null;

        if (!$this->hasCommand($commandName)) {
            throw new UnknownCommandException(\sprintf('Unknown command "%s" for game "%s" (options are %s).', $commandName, $this->getGameName(), implode(', ', array_keys($this->getCommands()))));
        }

        if (!$this->isInitialized) {
            $this->initialize();
        }

        return $this->getCommands()[$commandName]($args);
    }

    protected function getResourcesPath(): string
    {
        return $this->baseResourcePath.$this->getGameName().DIRECTORY_SEPARATOR;
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
