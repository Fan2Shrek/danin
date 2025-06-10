<?php

declare(strict_types=1);

namespace App\Service\Transport;

use App\Entity\RoomConfig;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class MercureTransport implements GameTransportInterface
{
    public function __construct(
        #[Target('mercure.hub.game')]
        private HubInterface $hub,
    ) {
    }

    public function send(RoomConfig $roomConfig, string $data, string $type): void
    {
        $this->hub->publish(new Update(
            (string) $roomConfig->getRoom()->getId(),
            $data,
        ));
    }
}
