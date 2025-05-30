<?php

declare(strict_types=1);

namespace App\Service\Redis\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AsRedisListener
{
    public function __construct(
        public string $event,
        public ?string $method = null,
    ) {
    }
}
