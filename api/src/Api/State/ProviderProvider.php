<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Provider;
use App\Service\Provider\ProviderManager;

final class ProviderProvider implements ProviderInterface
{
    public function __construct(
        private ProviderManager $providerManager,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return array_map(
            fn (string $name) => new Provider($name),
            $this->providerManager->getAll()
        );
    }
}
