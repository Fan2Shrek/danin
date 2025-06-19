<?php

declare(strict_types=1);

namespace App\Service\Bot;

use App\Domain\Model\Message as ModelMessage;
use App\Entity\Room;
use App\Service\Message\MessageProcessor;
use App\Service\Room\RoomTokenManager;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Psr\Log\LoggerInterface;

final class DiscordBot
{
    private const string NAME = 'Discord';

    /**
     * @var array<string, Room>
     */
    private array $rooms = [];

    public function __construct(
        private string $token,
        private LoggerInterface $logger,
        private MessageProcessor $messageProcessor,
        private RoomTokenManager $roomTokenManager,
    ) {
    }

    public function start(): never
    {
        $bot = new Discord([
            'token' => $this->token,
            'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
        ]);

        $bot->on('init', function (Discord $discord) {
            $discord->on(Event::MESSAGE_CREATE, $this->onMessage(...));
        });

        $bot->run();
    }

    private function onMessage(Message $message): void
    {
        if ($message->author->bot) {
            return;
        }

        $content = $message->content;
        if (str_starts_with($content, '/room ')) {
            $this->registerRoom(str_replace('/room ', '', $content), $message->channel_id);

            if (isset($this->rooms[$message->channel_id])) {
                $message->reply('Room registered successfully. You can now send messages to the room.');
            } else {
                $message->reply('Failed to register room. Please check the token and try again.');
            }

            return;
        }

        if (!$room = $this->rooms[$message->channel_id] ?? null) {
            $this->logger->warning('No room registered for channel {channelId}', [
                'channelId' => $message->channel_id,
            ]);

            $message->reply('No room registered for this channel. Please use `/room <token>` to register a room.');

            return;
        }

        $this->logger->info('Processing {content} from {author} received', [
            'author' => $message->author->username,
            'content' => $message->content,
        ]);

        $this->messageProcessor->process(new ModelMessage($message->content, $message->author->username), $room);
    }

    private function registerRoom(string $token, string $channelId): void
    {
        if (null !== ($this->rooms[$channelId] ?? null)) {
            return;
        }

        $room = $this->roomTokenManager->useToken($token);

        if (\in_array($room, $this->rooms, true)) {
            return;
        }

        $config = $room->getRoomConfig();
        if (!\in_array(self::NAME, $config->getProviders())) {
            return;
        }

        $this->rooms[$channelId] = $room;
    }
}
