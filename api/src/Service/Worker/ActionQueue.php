<?php

declare(strict_types=1);

namespace App\Service\Worker;

final class ActionQueue implements \Countable, \IteratorAggregate
{
    /** @var WorkerAction[] */
    private array $actions = [];

    public function addAction(WorkerAction $action): void
    {
        $this->actions[] = $action;
    }

    public function getNextAction(): ?WorkerAction
    {
        return array_shift($this->actions) ?: null;
    }

    public function count(): int
    {
        return \count($this->actions);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->actions);
    }
}
