<?php

declare(strict_types=1);

namespace App\Service\Redis\EventDispatcher;

readonly class RedisEvent
{
    public function __construct(
        public string $data,
    ) {
    }
}
