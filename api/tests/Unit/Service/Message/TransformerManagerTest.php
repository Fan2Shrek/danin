<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Message;

use App\Domain\Model\Message;
use App\Service\Message\Transformer\MessageTransformerInterface;
use App\Service\Message\Transformer\TransformerManager;
use PHPUnit\Framework\TestCase;

final class TransformerManagerTest extends TestCase
{
    public function testTransformerManager(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);
        $message = new Message('!test', 'test');

        $result = $manager->transform($message);

        self::assertSame(['true'], $result);
    }

    public function testTransformerManagerSupports(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);
        $message = new Message('!test', 'test');

        self::assertTrue($manager->supports($message));
    }

    public function testTransformerManagerDoesNotSupport(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new FalseTransformer(''),
        ]);
        $message = new Message('!test', 'test');

        self::assertFalse($manager->supports($message));
    }

    public function testGetCommands(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);

        $result = $manager->getCommandsFromGame('false');

        self::assertSame(['a', 'b'], $result);
    }
}

class FalseTransformer implements MessageTransformerInterface
{
    public function supports(Message $message): bool
    {
        return false;
    }

    public function transform(Message $message): array
    {
        return [];
    }

    public function getGameName(): string
    {
        return 'false';
    }

    public function getCommands(): array
    {
        return [
            'a' => 'var_dump',
            'b' => 'var_dump',
        ];
    }
}

class TrueTransformer implements MessageTransformerInterface
{
    public function supports(Message $message): bool
    {
        return true;
    }

    public function transform(Message $message): array
    {
        return ['true'];
    }

    public function getGameName(): string
    {
        return 'true';
    }

    public function getCommands(): array
    {
        return [];
    }
}
