<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Redis\EventDispatcher\RedisListenerManager;
use App\Service\Transport\GameTransportInterface;
use App\Service\Worker\DaninWorker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command is used to consume messages from the Danin queue.
 *
 * Thsi command should run continuously in the background to process messages as they arrive.
 */
#[AsCommand(
    name: 'app:danin:consumer',
    description: 'Consume messages from the Danin queue.',
)]
final class DaninConsumerCommand extends Command
{
    public function __construct(
        private RedisListenerManager $redisListenerManager,
        private GameTransportInterface $transport,
        private DaninWorker $worker,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Consume messages from the Danin queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting Danin consumer...');

        $this->worker->start();

        try {
            $this->redisListenerManager->startListening();
        } finally {
            $this->worker->shutdown();
        }

        return Command::SUCCESS;
    }
}
