<?php

declare(strict_types=1);

namespace App\Service\Redis\EventDispatcher;

use App\Service\Redis\RedisConnectionManager;
use Psr\Log\LoggerInterface;

final class RedisListenerManager
{
    /** @var array<string, callable(RedisEvent): void> */
    private array $listeners;

    public function __construct(
        private RedisConnectionManager $redis,
        private LoggerInterface $logger,
    ) {
    }

    public function addListener(string $event, callable|array $listener): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }

    public function startListening(): void
    {
        $this->redis->subscribe(array_keys($this->listeners), $this->dispatch(...));
    }

    private function callListenersForEvent(RedisEvent $event, string $eventName): void
    {
        foreach ($this->listeners[$eventName] as &$listener) {
            if (\is_array($listener) && $listener[0] instanceof \Closure) {
                $listener = [$listener[0](), $listener[1]];
            }
            $listener($event);
        }
    }

    private function dispatch(\Redis $redis, string $channel, string $msg): void
    {
        $this->logger->debug('Dispatching message', [
            'channel' => $channel,
            'message' => $redis,
            'stdout' => $msg,
        ]);

        try {
            $this->callListenersForEvent(
                unserialize($msg),
                $channel,
            );
        } catch (\Throwable $e) {
            $this->logger->error('Error while dispatching message', [
                'channel' => $channel,
                'message' => $redis,
                'stdout' => $msg,
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
            ]);
        }
    }
}
