<?php

declare(strict_types=1);

namespace App\Service\Redis\EventDispatcher;

use App\Service\Redis\RedisConnectionManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RedisEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private RedisConnectionManager $redis,
    ) {
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        if (!$event instanceof RedisEvent) {
            throw new \InvalidArgumentException('Event must be an instance of RedisEvent');
        }

        $this->redis->publish(
            $eventName,
            serialize($event),
        );

        return $event;
    }
}
