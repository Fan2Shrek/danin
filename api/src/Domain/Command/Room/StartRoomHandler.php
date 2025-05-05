<?php

declare(strict_types=1);

namespace App\Domain\Command\Room;

use App\Domain\Model\Connection;
use App\Entity\Config;
use App\Repository\ConfigRepository;
use App\Service\Transport\GameTransportInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class StartRoomHandler
{
    public function __construct(
        private Security $security,
        private GameTransportInterface $transport,
        private ConfigRepository $configRepository,
    ) {
    }

    public function __invoke(StartRoomCommand $command): void
    {
        $config = $this->configRepository->findOneByUser($this->security->getUser());

        if (null === $config) {
            $config = new Config($this->security->getUser());
        }

        $config->set('host', $command->host);
        $config->set('port', $command->port);

        $this->configRepository->save($config);
        $this->transport->send(new Connection($config->get('host'), $config->get('port')), json_encode([
            'host' => $command->host,
            'port' => $command->port,
        ]), 'create');
    }
}
