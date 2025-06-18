<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Bot\DiscordBot;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:run:discord')]
final class DiscordBotCommand extends Command
{
    public function __construct(
        private DiscordBot $discordBot,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->discordBot->start();

        return Command::SUCCESS;
    }
}
