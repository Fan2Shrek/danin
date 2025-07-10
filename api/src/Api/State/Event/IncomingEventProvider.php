<?php

declare(strict_types=1);

namespace App\Api\State\Event;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\EventRepository;

final class IncomingEventProvider implements ProviderInterface
{
    public function __construct(
        private EventRepository $eventRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->eventRepository->findBy(
            [],
            ['startAt' => 'ASC'],
            $context['filters']['limit'] ?? 10,
            $context['filters']['offset'] ?? 0
        );
    }
}
