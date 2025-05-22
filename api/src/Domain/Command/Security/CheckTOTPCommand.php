<?php

declare(strict_types=1);

namespace App\Domain\Command\Security;

final class CheckTOTPCommand
{
    public function __construct(
        public readonly string $code
    ) {
    }
}
