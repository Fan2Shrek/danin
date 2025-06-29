<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Transport;
use App\Service\Transport\DelagatingGameTransport;
use App\Service\Transport\GameTransportInterface;
use App\Service\Transport\ParametrableGameTransportInterface;
use App\Service\Transport\WorkerTransport;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class TransportProvider implements ProviderInterface
{
    /**
     * @param GameTransportInterface[] $transports
     */
    public function __construct(
        #[AutowireIterator(tag: 'app.transport')]
        private iterable $transports,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $data = array_reduce(
            iterator_to_array($this->transports),
            function ($acc, GameTransportInterface $t) {
                if (!\in_array($t::class, [DelagatingGameTransport::class, WorkerTransport::class], true)) {
                    $name = $t::class;
                    if (preg_match('/\\\\([a-zA-Z0-9]+)transport$/i', $name, $matches)) {
                        $name = $matches[1];
                    }
                    $name = strtolower($name);
                    $acc[$name] = new Transport(
                        $name,
                        $name,
                        $t instanceof ParametrableGameTransportInterface ? $t->getTransportSettings() : [],
                    );
                }

                return $acc;
            },
            [],
        );

        sort($data);

        return $data;
    }
}
