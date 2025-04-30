<?php

declare(strict_types=1);

namespace App\Service\Exception;

class UnknowConnectionException extends \RuntimeException
{
    public function __construct(
        private string $connectionId,
        string $message = 'Unknown connection with id "%s"',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(\sprintf($message, $connectionId), $code, $previous);
    }
}
