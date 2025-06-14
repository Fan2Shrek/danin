<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Message;

use App\Domain\Model\Message;
use App\Entity\Room;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;
use App\Service\Message\Transformer\IsaacMessageTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IsaacMessageTransformerTest extends TestCase
{
    #[DataProvider('spawnProvider')]
    public function testSpawn(int $expectedId, string $entityName): void
    {
        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message("!spawn $entityName", 'test');

        $result = $isaac->transform($message, $this->getRoomConfig());

        self::assertSame([
            'type' => 'spawn',
            'content' => $expectedId,
        ], $result);
    }

    public static function spawnProvider(): \Generator
    {
        yield 'one' => [1, 'one'];
        yield 'two' => [2, 'two'];
        yield 'three' => [3, 'three'];
    }

    public function testUnknownEntity(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown entity "unknown".');

        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!spawn unknown', 'test');

        $isaac->transform($message, $this->getRoomConfig());
    }

    public function testBomb(): void
    {
        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!bomb', 'test');

        $result = $isaac->transform($message, $this->getRoomConfig());

        self::assertSame([
            'type' => 'spawn',
            'content' => 173,
        ], $result);
    }

    #[DataProvider('useProvider')]
    public function testUse(?int $expectedId, ?string $activeItemName): void
    {
        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message("!use $activeItemName", 'test');

        $result = $isaac->transform($message, $this->getRoomConfig());

        self::assertSame([
            'type' => 'activate',
            'content' => $expectedId,
        ], $result);
    }

    public static function useProvider(): \Generator
    {
        yield 'faint' => [12, 'faint'];
        yield 'rhinestone eye' => [173, 'rhinestone_eye'];
    }

    public function testUseWithoutActiveItem(): void
    {
        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!use', 'test');

        $result = $isaac->transform($message, $this->getRoomConfig());

        self::assertSame([
            'type' => 'activate',
            'content' => null,
        ], $result);
    }

    public function testUseWithUnknwonItem(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown active item "unknown".');

        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!use unknown', 'test');

        $result = $isaac->transform($message, $this->getRoomConfig());

        self::assertSame([
            'type' => 'activate',
            'content' => null,
        ], $result);
    }

    public function testPlay(): void
    {
        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!play test_sound_1', 'test');

        $result = $isaac->transform($message, $this->getRoomConfig());

        self::assertSame([
            'type' => 'sound',
            'content' => 101,
        ], $result);
    }

    public function testPlayWithUnknownSound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown sound "unknown".');

        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!play unknown', 'test');

        $isaac->transform($message, $this->getRoomConfig());
    }

    private function getRoomConfig(): RoomConfig
    {
        return new RoomConfig(
            $this->createMock(Room::class),
            'mercure',
            GameEnum::THE_BINDING_OF_ISAAC,
            [],
            [
                'use',
                'spawn',
                'bomb',
                'play',
            ],
        );
    }
}
