<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

final readonly class RegisterCommand
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
    ) {
    }
}
