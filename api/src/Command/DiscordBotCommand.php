<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Model\Message as ModelMessage;
use App\Service\MessageProcessor;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:discord-bot')]
final class DiscordBotCommand extends Command
{
    public function __construct(
        private string $botToken,
        private LoggerInterface $logger,
        private MessageProcessor $messageProcessor,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bot = new Discord([
            'token' => $this->botToken,
            'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
        ]);

        $bot->on('init', function (Discord $discord) use ($output) {
            $output->writeln('Bot is ready!');

            $discord->on(Event::MESSAGE_CREATE, $this->onMessage(...));
        });

        $bot->run();

        return Command::SUCCESS;
    }

    private function onMessage(Message $message): void
    {
        if ($message->author->bot) {
            return;
        }

        $this->logger->info('Message {content} from {author} received', [
            'author' => $message->author->username,
            'content' => $message->content,
        ]);

        $this->messageProcessor->process(new ModelMessage($message->content, null));
    }
}
