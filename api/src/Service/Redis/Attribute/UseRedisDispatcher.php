<?php

declare(strict_types=1);

namespace App\Service\Redis\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class UseRedisDispatcher
{
    public function __construct(
        public bool $throwIfNotFound = false,
    ) {
    }
}
