<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\Security\Core\User\UserInterface;

readonly class Message
{
    public function __construct(
        public string $content,
        public UserInterface $author,
    ) {
    }
}
