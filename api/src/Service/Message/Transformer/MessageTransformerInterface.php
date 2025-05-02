<?php

declare(strict_types=1);

namespace App\Service\Message\Transformer;

use App\Domain\Model\Message;

interface MessageTransformerInterface
{
    public function transform(Message $message): array;

    public function supports(Message $message): bool;
}
