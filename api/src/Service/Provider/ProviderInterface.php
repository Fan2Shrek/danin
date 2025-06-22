<?php

declare(strict_types=1);

namespace App\Service\Provider;

interface ProviderInterface
{
    public function getName(): string;

    public function start(): void;
}
