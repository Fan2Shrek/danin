<?php

declare(strict_types=1);

namespace App\Service\Worker;

readonly class WorkerAction
{
    public function __construct(
        public string $id,
        public string $serverId,
        public string $type,
        public array $data,
    ) {
    }
}
