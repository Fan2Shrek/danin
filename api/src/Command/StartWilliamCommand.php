<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Provider\ProviderManager;
use App\Service\Worker\DaninWorker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpSubprocess;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:start:william',
    description: 'Starts the William service',
    aliases: ['app:william:start'],
    hidden: false,
)]
final class StartWilliamCommand extends Command
{
    private array $processes = [];
    private bool $shouldStop = false;

    private OutputInterface $output;

    public function __construct(
        private ProviderManager $providerManager,
        private DaninWorker $worker,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $this->startDaninConsumer();
        $this->startProviders();

        while (!$this->shouldStop) {
            foreach ($this->processes as $name => $process) {
                if (!$process->isRunning()) {
                    $this->output->writeln(\sprintf('Process %s has stopped.', $name));
                    unset($this->processes[$name]);
                }
            }
        }

        return self::SUCCESS;
    }

    private function startDaninConsumer(): void
    {
        $this->startProcess(
            'consumer',
            new PhpSubprocess(['bin/console', 'app:danin:consumer']),
        );
    }

    private function startProviders(): void
    {
        foreach ($this->providerManager->getAll() as $provider) {
            $this->startProcess(
                $provider,
                new PhpSubprocess(['bin/console', 'app:run:'.$provider]),
            );
        }
    }

    private function startProcess(string $name, Process $process): void
    {
        $this->processes[$name] = $process;
        $this->output->writeln(\sprintf('Starting process: %s', $name));
        $process->start(fn ($type, $buffer) => $this->doLog($name, $type, $buffer));
    }

    private function doLog(string $name, $type, $buffer): void
    {
        $this->output->writeln(\sprintf('[%s] %s', $name, $buffer));
    }
}
