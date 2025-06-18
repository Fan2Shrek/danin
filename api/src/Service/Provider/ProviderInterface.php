<?php

declare(strict_types=1);

namespace App\Service\Provider;

interface ProviderInterface
{
    public function getName(): string;

    public function start(): void;

    /**
     * Return the commands to setup the provider.
     */
    public function setup(): string;
}
