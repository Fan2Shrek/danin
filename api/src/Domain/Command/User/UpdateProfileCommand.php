<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

final class UpdateProfileCommand
{
    public function __construct(
        public ?string $username = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {
    }
}
