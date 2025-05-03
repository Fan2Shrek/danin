<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Message;

use App\Domain\Model\Message;
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

        $result = $isaac->transform($message);

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

        $isaac->transform($message);
    }

    public function testBomb(): void
    {
        $isaac = new IsaacMessageTransformer(\dirname(__DIR__, 3).'/Resources/games/');
        $message = new Message('!bomb', 'test');

        $result = $isaac->transform($message);

        self::assertSame([
            'type' => 'spawn',
            'content' => 173,
        ], $result);
    }
}
