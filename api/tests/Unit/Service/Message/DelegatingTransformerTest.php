<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Message;

use App\Domain\Model\Message;
use App\Service\Message\Transformer\DelegatingTransformer;
use App\Service\Message\Transformer\MessageTransformerInterface;
use PHPUnit\Framework\TestCase;

final class DelegatingTransformerTest extends TestCase
{
    public function testDelegatingTransformer(): void
    {
        $delagatoer = new DelegatingTransformer([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);
        $message = new Message('!test', 'test');

        $result = $delagatoer->transform($message);

        self::assertSame(['true'], $result);
    }

    public function testDelegatingTransformerSupports(): void
    {
        $delagatoer = new DelegatingTransformer([
            new FalseTransformer(''),
            new TrueTransformer(''),
        ]);
        $message = new Message('!test', 'test');

        self::assertTrue($delagatoer->supports($message));
    }

    public function testDelegatingTransformerDoesNotSupport(): void
    {
        $delagatoer = new DelegatingTransformer([
            new FalseTransformer(''),
            new FalseTransformer(''),
        ]);
        $message = new Message('!test', 'test');

        self::assertFalse($delagatoer->supports($message));
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
}
