<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Model\MercureToken;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\HubRegistry;

final class MercureTokenProcessor implements ProcessorInterface
{
    public function __construct(
        private Authorization $authorization,
        private HubRegistry $hubRegistry,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof MercureToken) {
            throw new \InvalidArgumentException('Expected instance of MercureToken.');
        }

        $this->authorization->setCookie(
            $context['request'],
            $data->topics,
        );

        return $data;
    }
}
