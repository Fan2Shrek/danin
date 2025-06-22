<?php

declare(strict_types=1);

namespace App\Service\Provider;

final class ProviderManager
{
    /**
     * @param iterable<ProviderInterface> $providers
     */
    public function __construct(
        private iterable $providers,
    ) {
    }

    /**
     * @return string[]
     */
    public function getAll(): array
    {
        $names = [];
        foreach ($this->providers as $provider) {
            $names[] = $provider->getName();
        }

        return $names;
    }

    public function startAll(): void
    {
        foreach ($this->providers as $provider) {
            $provider->start();
        }
    }

    public function getProvider(string $name): ?ProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($name === $provider->getName()) {
                return $provider;
            }
        }

        return null;
    }
}
