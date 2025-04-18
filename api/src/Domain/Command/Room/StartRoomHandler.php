<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

use App\Service\Transport\GameTransportInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class StartRoomHandler
{
    public function __construct(
        private GameTransportInterface $transport,
    ) {
    }

    public function __invoke(StartRoomCommand $command): void
    {
        // maybe generate a room id?
        // + todo RoomConfig entity
        $config = [
            'host' => '172.17.0.1',
            'port' => 12345,
        ];

        $this->transport->send('extras', json_encode($config), 'create');
    }
}
