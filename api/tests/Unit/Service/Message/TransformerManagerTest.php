<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Message;

use App\Domain\Model\Message;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;
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

        $result = $manager->transform($message, $this->createMock(RoomConfig::class));

        self::assertSame(['true'], $result);
    }

    public function testTransformerManagerSupports(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);

        self::assertTrue($manager->supports($this->createMock(RoomConfig::class)));
    }

    public function testTransformerManagerDoesNotSupport(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new FalseTransformer(''),
        ]);

        self::assertFalse($manager->supports($this->createMock(RoomConfig::class)));
    }

    public function testGetCommands(): void
    {
        $manager = new TransformerManager([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);

        $result = $manager->getCommandsFromGame(GameEnum::THE_BINDING_OF_ISAAC);

        self::assertSame(['a', 'b'], $result);
    }
}

class FalseTransformer implements MessageTransformerInterface
{
    public function supports(RoomConfig $config): bool
    {
        return false;
    }

    public function transform(Message $message, RoomConfig $roomConfig): array
    {
        return [];
    }

    public function getGame(): GameEnum
    {
        return GameEnum::THE_BINDING_OF_ISAAC;
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
    public function supports(RoomConfig $config): bool
    {
        return true;
    }

    public function transform(Message $message, RoomConfig $roomConfig): array
    {
        return ['true'];
    }

    public function getGame(): GameEnum
    {
        return GameEnum::THE_BINDING_OF_ISAAC;
    }

    public function getCommands(): array
    {
        return [];
    }
}
